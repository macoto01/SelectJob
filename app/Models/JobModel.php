<?php
// app/Models/JobModel.php

class JobModel {

    /** 求人一覧（会社名・タグ含む） */
    public function findAll(array $filters = []): array {
        $where  = ['j.status = :status'];
        $params = [':status' => 'active'];

        if (!empty($filters['keyword'])) {
            $where[]              = '(j.title LIKE :kw OR c.name LIKE :kw OR j.work_description LIKE :kw)';
            $params[':kw']        = '%' . $filters['keyword'] . '%';
        }
        if (!empty($filters['location'])) {
            $where[]              = 'j.location LIKE :loc';
            $params[':loc']       = '%' . $filters['location'] . '%';
        }
        if (isset($filters['remote']) && $filters['remote'] === '1') {
            $where[] = 'j.remote_work = 1';
        }
        if (isset($filters['flex']) && $filters['flex'] === '1') {
            $where[] = 'j.flex_time = 1';
        }

        $allowedSort = ['created_at DESC', 'salary_max DESC', 'created_at ASC'];
        $sort = in_array($filters['sort'] ?? '', $allowedSort, true) ? $filters['sort'] : 'created_at DESC';

        $sql = 'SELECT j.*, c.name AS company_name
                FROM jobs j
                JOIN companies c ON c.id = j.company_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY j.' . $sort;

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $jobs = $stmt->fetchAll();

        // タグ付与
        foreach ($jobs as &$job) {
            $job['tags'] = $this->findTagsByJobId($job['id']);
        }
        return $jobs;
    }

    /** 求人1件（会社情報含む） */
    public function findById(int $id): ?array {
        $sql = 'SELECT j.*, c.name AS company_name,
                    c.description AS company_description,
                    c.industry, c.employees, c.founded, c.website, c.address
                FROM jobs j
                JOIN companies c ON c.id = j.company_id
                WHERE j.id = :id AND j.status = :status
                LIMIT 1';
        $stmt = db()->prepare($sql);
        $stmt->execute([':id' => $id, ':status' => 'active']);
        $job = $stmt->fetch();
        if (!$job) return null;

        $job['tags'] = $this->findTagsByJobId($id);
        return $job;
    }

    /** 応募済みかどうか（セッションのダミー実装） */
    public function isApplied(int $jobId): bool {
        $applied = $_SESSION['applied'] ?? [];
        return in_array($jobId, $applied, true);
    }

    /** 応募を記録（セッション） */
    public function apply(int $jobId): void {
        $_SESSION['applied'][] = $jobId;
    }

    private function findTagsByJobId(int $jobId): array {
        $stmt = db()->prepare('SELECT tag FROM job_tags WHERE job_id = :id');
        $stmt->execute([':id' => $jobId]);
        return array_column($stmt->fetchAll(), 'tag');
    }
}
