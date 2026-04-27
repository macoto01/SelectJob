<?php
namespace App\Controllers;
use App\Models\AuthModel;

class AuthController extends BaseController {
    private AuthModel $auth;

    public function __construct() {
        parent::__construct();
        $this->auth = new AuthModel();
    }

    public function loginForm(): void {
        if (auth_check()) {
            $this->redirect('/');
        }
        $flash = $this->getFlash();
        $this->renderBlank('auth/login', compact('flash'));
    }

    public function loginPost(): void {
        $this->verifyCsrf();
        $user = $this->auth->login($this->post('email'), $this->post('password'), $_SERVER['REMOTE_ADDR']);

        if (!$user) {
            $msg = $_SESSION['login_error'] ?? 'メールアドレスまたはパスワードが正しくありません。';
            unset($_SESSION['login_error']);
            $this->flash($msg, 'error');
            $this->redirect('/login');
        }

        $this->redirect('/');
    }

    public function logout(): void {
        $this->auth->logout();
        $this->redirect('/login');
    }

    public function forgotPasswordForm(): void {
        if (auth_check()) {
            $this->redirect('/');
        }
        $flash = $this->getFlash();
        $this->renderBlank('auth/forgot_password', compact('flash'));
    }

    public function forgotPasswordPost(): void {
        $this->verifyCsrf();
        $email = $this->post('email');

        $user = $this->auth->findActiveByEmail($email);

        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->auth->createPasswordReset($email, $token, $expires);

            $appUrl  = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
            $fullUrl = $appUrl . base_url('/reset-password?token=' . $token);
            
            $body = "こんにちは、{$user['name']} 様\n\nパスワードリセット用リンク:\n{$fullUrl}\n\nこのリンクは1時間有効です。\n心当たりがない場合は無視してください。\n\nSelectJob";
            @mail($email, '【SelectJob】パスワードリセットのご案内', $body, "From: noreply@SelectJob.jp\r\nContent-Type: text/plain; charset=UTF-8");
        }
        $this->flash('メールアドレスにリセット用リンクを送信しました。');
        $this->redirect('/forgot-password');
    }

    public function resetPasswordForm(): void {
        if (auth_check()) {
            $this->redirect('/');
        }
        $token      = $this->get('token');
        $flash      = $this->getFlash();
        $validToken = $this->auth->validateResetToken($token);
        
        $this->renderBlank('auth/reset_password', compact('flash', 'token', 'validToken'));
    }

    public function resetPasswordPost(): void {
        $this->verifyCsrf();
        $token   = $this->post('token');
        $new     = $this->post('new_password');
        $confirm = $this->post('new_password_confirm');
        
        $tokenRow = $this->auth->validateResetToken($token);
        if (!$tokenRow) {
            $this->flash('このリンクは無効または期限切れです。', 'error');
            $this->redirect('/forgot-password');
        }
        
        if (strlen($new) < 6) {
            $this->flash('パスワードは6文字以上で入力してください。', 'error');
            $this->redirect('/reset-password?token=' . urlencode($token));
        }
        
        if ($new !== $confirm) {
            $this->flash('パスワードが一致しません。', 'error');
            $this->redirect('/reset-password?token=' . urlencode($token));
        }
        
        $this->auth->resetPassword($tokenRow['email'], $new, $token);
        
        $this->flash('パスワードを変更しました。新しいパスワードでログインしてください。');
        $this->redirect('/login');
    }
}
