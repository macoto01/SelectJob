<?php
namespace App\Models;

class CompanyModel extends BaseModel {
    public function findAll(): array { return $this->db->query('SELECT * FROM companies ORDER BY name')->fetchAll(); }
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM companies WHERE id=? LIMIT 1');
        $stmt->execute([$id]); return $stmt->fetch() ?: null;
    }
}
