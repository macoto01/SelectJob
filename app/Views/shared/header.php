<?php
if (!headers_sent()) header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8'); mb_http_output('UTF-8');
?><!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SelectJob - 転職管理マイページ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= h(base_url('/css/style.css')) ?>">
</head>
<body>
