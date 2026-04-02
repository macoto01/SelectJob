<?php
// app/Models/CompanyModel.php

class CompanyModel {

    public function findById(int $id): ?array {
        $stmt = db()->prepare('SELECT * FROM companies WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findJobsByCompany(int $companyId): array {
        $stmt = db()->prepare(
            'SELECT j.*, c.name AS company_name
            FROM jobs j JOIN companies c ON c.id = j.company_id
            WHERE j.company_id = :cid AND j.status = :s ORDER BY j.created_at DESC'
        );
        $stmt->execute([':cid' => $companyId, ':s' => 'active']);
        return $stmt->fetchAll();
    }
}
