<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SelectJob - 転職管理マイページ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= h(base_url('/css/style.css')) ?>">
</head>
<body>
<?php
// ヘルパー関数（ビュー共通）
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function base_url(string $path = ''): string {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $base . $path;
}
function salary_label(?int $min, ?int $max): string {
    if (!$min && !$max) return '応相談';
    if ($min && $max)   return $min . '万〜' . $max . '万円';
    if ($min)           return $min . '万円〜';
    return '〜' . $max . '万円';
}
?>
