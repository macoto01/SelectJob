<?php
/**
 * app/Helpers/helpers.php
 */
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function base_url(string $path = ''): string {
    static $base = null;
    if ($base === null) {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    }
    return $base . $path;
}
function salary_label(?int $min, ?int $max): string {
    if (!$min && !$max) return '応相談';
    if ($min && $max)   return $min . '万〜' . $max . '万円';
    if ($min)           return $min . '万円〜';
    return '〜' . $max . '万円';
}
function ym(?int $y, ?int $m, bool $isCurrent = false): string {
    if ($isCurrent) return '現在';
    if (!$y)        return '―';
    return $y . '年' . ($m ? $m . '月' : '');
}
function salary_band(?int $salary): string {
    if (!$salary) return '';
    $bands = [300=>'300万円未満',400=>'300万円台',500=>'400万円台',
              600=>'500万円台',700=>'600万円台',800=>'700万円台',
              900=>'800万円台',1000=>'900万円台'];
    foreach ($bands as $threshold => $label) {
        if ($salary < $threshold) return $salary . '万円（' . $label . '）';
    }
    return $salary . '万円（1000万円以上）';
}
function auth_user(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return $_SESSION['auth_user'] ?? null;
}
function auth_check(): bool { return auth_user() !== null; }
function auth_check_role(string $role): bool {
    $user = auth_user();
    return $user !== null && $user['role'] === $role;
}

/**
 * 言語ファイル (lang/ja/labels.php) から値を取得するグローバルヘルパー
 */
function lang(string $key): mixed {
    static $labels = null;
    if ($labels === null) {
        $path = BASE_PATH . '/lang/ja/labels.php';
        $labels = file_exists($path) ? require $path : [];
    }
    $keys = explode('.', $key);
    $val = $labels;
    foreach ($keys as $k) {
        if (!is_array($val) || !isset($val[$k])) {
            return $key;
        }
        $val = $val[$k];
    }
    return $val;
}

/**
 * SVGアイコンを表示する
 * 名前（'settings'など）または生のパスデータを指定可能。$size でサイズを強制指定可能。
 */
function icon(string $nameOrPath, string $class = 'nav-icon', ?int $size = null): string {
    static $iconMap = [
        'settings' => 'M8 1a1 1 0 00-1 1v.5a4.5 4.5 0 00-1.4.8L5 2.7a1 1 0 00-1.4 0L2.7 3.6A1 1 0 002.7 5l.4.4A4.5 4.5 0 003 6.5H2.5a1 1 0 000 2H3a4.5 4.5 0 00.8 1.4L3 10.6a1 1 0 000 1.4l.9.9a1 1 0 001.4 0l.4-.4A4.5 4.5 0 007 13v.5a1 1 0 002 0V13a4.5 4.5 0 001.4-.8l.4.4a1 1 0 001.4 0l.9-.9a1 1 0 000-1.4l-.4-.4A4.5 4.5 0 0013 9h.5a1 1 0 000-2H13a4.5 4.5 0 00-.8-1.4l.4-.4A1 1 0 0013 3.6L12 2.7a1 1 0 00-1.4 0l-.4.4A4.5 4.5 0 009 2.5V2a1 1 0 00-1-1zm0 5a2 2 0 110 4 2 2 0 010-4z',
        'logout'   => 'M6 2H2v12h4v1H1V1h5V2zm4 9l4-3-4-3v2H5V8h5V7l4 3-4 3v-1z',
    ];

    // マップにあればそれを使用、なければ引数をそのままパスとして扱う
    $path = $iconMap[$nameOrPath] ?? $nameOrPath;

    $sizeAttr = $size ? ' width="' . $size . '" height="' . $size . '"' : '';

    return '<svg class="' . h($class) . '"' . $sizeAttr . ' viewBox="0 0 16 16" fill="currentColor"><path d="' . $path . '"/></svg>';
}

// ユーザーステータスのラベルを取得するヘルパー
if (!function_exists('user_label')) {
    function user_label(string $key): string {
        $label = lang("user_status.{$key}");
        return is_string($label) ? $label : $key;
    }
}
