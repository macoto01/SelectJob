<?php
// config/database.php

// Docker環境を優先したデフォルト値の設定
define('DB_HOST',    getenv('DB_HOST')    ?: 'db'); 
define('DB_PORT',    getenv('DB_PORT')    ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: 'jobnext');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: 'password'); 
define('DB_CHARSET', 'utf8mb4');

/**
 * データベース接続（PDO）を取得する
 */
function db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
            ]);
        } catch (PDOException $e) {
            // ステージング環境等でエラー詳細を出しすぎないための配慮
            error_log("Database Connection Error: " . $e->getMessage());
            die("データベース接続に失敗しました。管理者に問い合わせてください。");
        }
    }
    return $pdo;
}