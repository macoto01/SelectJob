<?php
namespace App\Models;

class JobModel extends BaseModel {
    public function findAll(array $filters = []): array {
        $where_conditions = ["j.status = 'active'"]; 
        $parameters = [];
        if (!empty($filters['keyword'])) {
            $where_conditions[] = "(j.title LIKE :keyword_title OR c.name LIKE :keyword_company_name OR j.location LIKE :keyword_location)";
            $keyword_pattern = '%' . $filters['keyword'] . '%';
            $parameters[':keyword_title'] = $keyword_pattern;
            $parameters[':keyword_company_name'] = $keyword_pattern;
            $parameters[':keyword_location'] = $keyword_pattern;
        }
        if (!empty($filters['location'])) { 
            $where_conditions[] = "j.location LIKE :location"; 
            $parameters[':location'] = '%' . $filters['location'] . '%'; 
        }
        if (!empty($filters['remote'])) { $where_conditions[] = "j.remote_work = 1"; }
        if (!empty($filters['flex']))   { $where_conditions[] = "j.flex_time = 1"; }
        
        $order = match($filters['sort'] ?? '') { 
            'salary_max DESC' => 'j.salary_max DESC', 
            'created_at ASC' => 'j.created_at ASC', 
            default => 'j.created_at DESC' 
        };

        $sql = "SELECT j.*, c.name AS company_name FROM jobs j JOIN companies c ON c.id = j.company_id WHERE " . implode(' AND ', $where_conditions) . " ORDER BY $order";
        $statement = $this->db->prepare($sql); 
        $statement->execute($parameters);
        $job_list = $statement->fetchAll();

        foreach ($job_list as &$job_row) {
            $tag_statement = $this->db->prepare('SELECT tag FROM job_tags WHERE job_id = ?'); 
            $tag_statement->execute([$job_row['id']]);
            $job_row['tags'] = array_column($tag_statement->fetchAll(), 'tag');
        }
        return $job_list;
    }
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT j.*,c.name AS company_name,c.description AS company_desc,c.industry,c.employees,c.founded,c.website,c.address AS company_address FROM jobs j JOIN companies c ON c.id=j.company_id WHERE j.id=? LIMIT 1');
        $stmt->execute([$id]); $job = $stmt->fetch(); if (!$job) return null;
        $s = $this->db->prepare('SELECT tag FROM job_tags WHERE job_id=?'); $s->execute([$id]);
        $job['tags'] = array_column($s->fetchAll(),'tag'); return $job;
    }
    public function isApplied(int $jobId): bool {
        $me = auth_user(); if (!$me) return false;
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM applications WHERE user_id=? AND job_id=?');
        $stmt->execute([$me['id'],$jobId]); return (int)$stmt->fetchColumn()>0;
    }
    public function apply(int $jobId): void {
        $me = auth_user(); if (!$me) return;
        $stmt = $this->db->prepare('INSERT IGNORE INTO applications (user_id,job_id) VALUES (?,?)');
        $stmt->execute([$me['id'],$jobId]);
    }
}
