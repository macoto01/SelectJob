<?php
namespace App\Controllers;

/**
 * app/Controllers/BaseController.php
 *
 * セキュリティ機能:
 *   - CSRFトークン生成・検証
 *   - セッションタイムアウト（30分）
 */
abstract class BaseController {

    // セッションタイムアウト（秒）
    private const SESSION_TIMEOUT = 1800; // 30分

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
        $this->checkSessionTimeout();
    }

    // ─────────────────────────────────────────
    // セッションタイムアウト
    // ─────────────────────────────────────────

    /**
     * 最終アクティビティから SESSION_TIMEOUT 秒経過していたらセッションを破棄する。
     * ログアウトページ・ログインページ・パスワードリセットページは対象外。
     */
    private function checkSessionTimeout(): void {
        // 未ログインなら不要
        if (!isset($_SESSION['auth_user'])) {
            return;
        }

        $now = time();
        if (isset($_SESSION['last_activity'])) {
            if ($now - $_SESSION['last_activity'] > self::SESSION_TIMEOUT) {
                // タイムアウト → セッション破棄してログイン画面へ
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $p = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $p['path'], $p['domain'], $p['secure'], $p['httponly']);
                }
                session_destroy();
                // 再スタートしてフラッシュメッセージをセット
                session_start();
                $_SESSION['flash']      = $this->lang('auth.session_timeout');
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . base_url('/login'));
                exit;
            }
        }
        // 最終アクティビティを更新
        $_SESSION['last_activity'] = $now;
    }

    // ─────────────────────────────────────────
    // CSRF トークン
    // ─────────────────────────────────────────

    /**
     * CSRFトークンを生成してセッションに保存し返す。
     * layout.php と renderBlank 経由で全フォームに埋め込まれる。
     */
    public static function csrfToken(): string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * CSRFトークンを検証する。
     * 失敗時は 403 を返して終了。
     */
    protected function verifyCsrf(): void {
        $token      = $_POST['_csrf'] ?? '';
        $sessionTok = $_SESSION['csrf_token'] ?? '';

        if ($sessionTok === '' || !hash_equals($sessionTok, $token)) {
            $this->abort403();
        }
    }

    /**
     * 403 Forbidden エラーを表示して終了。
     */
    protected function abort403(): void {
        http_response_code(403);
        $this->renderBlank('shared/403', [
            'title'   => $this->lang('system.error_403_title'),
            'message' => $this->lang('error.csrf_invalid'),
            'link'    => $this->lang('system.back_to_top')
        ]);
        exit;
    }

    // ─────────────────────────────────────────
    // ビュー描画
    // ─────────────────────────────────────────

    protected function render(string $view, array $viewParams = []): void {
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            $this->abort404(); // ビューファイルが存在しない場合は404
        }

        extract($viewParams);

        // 先に個別のViewを読み込んで出力をバッファに貯める
        // これにより、View内で定義された $activeNav や $contentClass が以下の require で利用可能になる
        ob_start();
        require $viewFile;
        $viewContent = ob_get_clean();

        require BASE_PATH . '/app/Views/shared/header.php';
        require BASE_PATH . '/app/Views/shared/layout.php';
        require BASE_PATH . '/app/Views/shared/footer.php';
    }

    protected function renderRaw(string $view, array $viewParams = []): void {
        extract($viewParams);
        require BASE_PATH . '/app/Views/' . $view . '.php';
    }

    protected function renderBlank(string $view, array $viewParams = []): void {
        extract($viewParams);
        require BASE_PATH . '/app/Views/shared/header.php';
        require BASE_PATH . '/app/Views/' . $view . '.php';
        require BASE_PATH . '/app/Views/shared/footer.php';
    }

    // ─────────────────────────────────────────
    // リダイレクト・フラッシュ
    // ─────────────────────────────────────────

    protected function redirect(string $path): void {
        header('Location: ' . base_url($path));
        exit;
    }

    protected function flash(string $message, string $type = 'success'): void {
        $_SESSION['flash']      = $message;
        $_SESSION['flash_type'] = $type;
    }

    protected function getFlash(): array {
        $msg  = $_SESSION['flash']      ?? '';
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash'], $_SESSION['flash_type']);
        return ['message' => $msg, 'type' => $type];
    }

    // ─────────────────────────────────────────
    // 入力値
    // ─────────────────────────────────────────

    protected function post(string $key, mixed $default = ''): mixed {
        $val = $_POST[$key] ?? $default;
        return is_string($val) ? trim($val) : $val;
    }

    protected function get(string $key, mixed $default = ''): mixed {
        $val = $_GET[$key] ?? $default;
        return is_string($val) ? trim($val) : $val;
    }

    // ─────────────────────────────────────────
    // 認証ガード
    // ─────────────────────────────────────────

    protected function requireLogin(): void {
        if (!auth_check()) {
            $this->flash($this->lang('auth.require_login'), 'error');
            $this->redirect('/login');
        }
    }

    protected function requireUser(): void {
        $this->requireLogin();
        if (!auth_check_role('user')) {
            $this->flash("ユーザー専用のページです。", 'error');
            $this->redirect('/');
        }
    }

    protected function requireAdmin(): void {
        $this->requireLogin();
        if (!auth_check_role('admin')) {
            $this->flash($this->lang('auth.require_admin'), 'error');
            $this->redirect('/');
        }
    }

    protected function abort404(): void {
        http_response_code(404);
        $this->renderBlank('shared/404');
        exit;
    }
    
    /**
     * 言語ファイルから値を取得する
     */
    protected function lang(string $key): string {
        $translation = \lang($key); // グローバルヘルパーのlang関数を呼び出す
        return is_string($translation) ? $translation : $key;
    }

    /**
     * JSONレスポンスを返して終了する
     */
    protected function jsonResponse(mixed $payload): void {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * ページネーション処理の実行
     * 
     * @param int $total 全件数
     * @param int $perPage 1ページあたりの表示件数
     * @return array 構築されたPagerデータ
     */
    protected function paginate(int $total, int $perPage = 20): array {
        return $this->getPaginationData($total, $perPage, (int)$this->get('page', 1));
    }

    /**
     * ページネーションに必要な計算値を算出する
     * 
     * @param int $total 全件数
     * @param int $perPage 1ページあたりの表示件数
     * @param int $currentPage 現在のページ番号
     * @return array 計算結果
     */
    private function getPaginationData(int $total, int $perPage, int $currentPage): array {
        $lastPage = (int)max(1, ceil($total / $perPage));
        $page     = (int)max(1, min($currentPage, $lastPage));
        $offset   = ($page - 1) * $perPage;

        return [
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => $lastPage,
            'offset'       => $offset,
            'from'         => $total > 0 ? $offset + 1 : 0,
            'to'           => (int)min($offset + $perPage, $total),
        ];
    }
}
