<?php
namespace App\Models;

abstract class BaseModel {
    /**
     * @var \PDO
     */
    protected \PDO $db;

    public function __construct() {
        // グローバルな db() ヘルパーを使用して PDO インスタンスを取得
        $this->db = db();
    }
}