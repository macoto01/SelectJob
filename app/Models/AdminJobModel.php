<?php
namespace App\Models;

class AdminJobModel extends BaseModel {
    public function getAllCompanies(): array {
        return $this->db->query('SELECT id,name FROM companies ORDER BY name')->fetchAll();
    }
    public function searchCompanies(string $keyword = '', int $limit = 20, int $offset = 0): array {
        if ($keyword !== '') {
            $sql = 'SELECT c.*, COUNT(j.id) AS job_count FROM companies c LEFT JOIN jobs j ON j.company_id = c.id WHERE c.name LIKE :keyword_name OR c.industry LIKE :keyword_industry GROUP BY c.id ORDER BY c.name LIMIT :limit OFFSET :offset';
            $keyword_pattern = '%' . $keyword . '%';
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':keyword_name', $keyword_pattern);
            $statement->bindValue(':keyword_industry', $keyword_pattern);
        } else {
            $statement = $this->db->prepare('SELECT c.*, COUNT(j.id) AS job_count FROM companies c LEFT JOIN jobs j ON j.company_id = c.id GROUP BY c.id ORDER BY c.name LIMIT :limit OFFSET :offset');
        }
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function searchJobs(string $keyword = '', string $status = '', int $company_id = 0, int $limit = 20, int $offset = 0): array {
        $where_conditions = ['1=1']; 
        $parameters = [];
        if ($keyword!=='') { 
            $where_conditions[] = "(j.title LIKE :keyword_title OR c.name LIKE :keyword_company OR j.location LIKE :keyword_location)";
            $keyword_pattern = '%' . $keyword . '%';
            $parameters[':keyword_title'] = $keyword_pattern;
            $parameters[':keyword_company'] = $keyword_pattern;
            $parameters[':keyword_location'] = $keyword_pattern;
        }
        if ($status === 'active' || $status === 'closed') { 
            $where_conditions[] = 'j.status = :status'; 
            $parameters[':status'] = $status; 
        }
        if ($company_id > 0) { 
            $where_conditions[] = 'j.company_id = :company_id'; 
            $parameters[':company_id'] = $company_id; 
        }
        $sql = 'SELECT j.*, c.name AS company_name FROM jobs j JOIN companies c ON c.id = j.company_id WHERE ' . implode(' AND ', $where_conditions) . ' ORDER BY j.created_at DESC LIMIT :limit OFFSET :offset';
        $statement = $this->db->prepare($sql); 
        foreach($parameters as $key => $val) $statement->bindValue($key, $val);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function countJobs(string $keyword = '', string $status = '', int $company_id = 0): int {
        $where_conditions = ['1=1'];
        $parameters = [];
        if ($keyword!=='') {
            $where_conditions[] = "(j.title LIKE :k OR c.name LIKE :k OR j.location LIKE :k)";
            $parameters[':k'] = '%' . $keyword . '%';
        }
        if ($status === 'active' || $status === 'closed') {
            $where_conditions[] = 'j.status = :status';
            $parameters[':status'] = $status;
        }
        if ($company_id > 0) {
            $where_conditions[] = 'j.company_id = :company_id';
            $parameters[':company_id'] = $company_id;
        }
        $sql = 'SELECT COUNT(*) FROM jobs j JOIN companies c ON c.id = j.company_id WHERE ' . implode(' AND ', $where_conditions);
        $stmt = $this->db->prepare($sql); $stmt->execute($parameters);
        return (int)$stmt->fetchColumn();
    }
    public function findJobById(int $id): ?array {
        $stmt=$this->db->prepare('SELECT j.*,c.name AS company_name FROM jobs j JOIN companies c ON c.id=j.company_id WHERE j.id=? LIMIT 1');
        $stmt->execute([$id]); $job=$stmt->fetch(); if(!$job) return null;
        $s=$this->db->prepare('SELECT tag FROM job_tags WHERE job_id=?'); $s->execute([$id]);
        $job['tags']=array_column($s->fetchAll(),'tag'); return $job;
    }
    public function findCompanyById(int $id): ?array {
        $stmt=$this->db->prepare('SELECT * FROM companies WHERE id=? LIMIT 1'); $stmt->execute([$id]); return $stmt->fetch()?:null;
    }
    public function createCompany(array $data): int {
        $statement = $this->db->prepare('INSERT INTO companies (name, description, industry, employees, founded, website, address) VALUES (:name, :description, :industry, :employees, :founded, :website, :address)');
        $statement->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':industry' => $data['industry'] ?? '',
            ':employees' => $data['employees'] ?? '',
            ':founded' => $data['founded'] ?: null,
            ':website' => $data['website'] ?? '',
            ':address' => $data['address'] ?? ''
        ]);
        return (int)$this->db->lastInsertId();
    }
    public function updateCompany(int $id, array $data): void {
        $this->db->prepare('UPDATE companies SET name = :name, description = :description, industry = :industry, employees = :employees, founded = :founded, website = :website, address = :address WHERE id = :id')
            ->execute([
                ':name' => $data['name'],
                ':description' => $data['description'] ?? '',
                ':industry' => $data['industry'] ?? '',
                ':employees' => $data['employees'] ?? '',
                ':founded' => $data['founded'] ?: null,
                ':website' => $data['website'] ?? '',
                ':address' => $data['address'] ?? '',
                ':id' => $id
            ]);
    }
    public function createJob(array $data): int {
        $statement = $this->db->prepare('INSERT INTO jobs (company_id, title, job_type, salary_min, salary_max, location, work_description, job_change_scope, requirements, preferred, benefits, working_hours, holiday, remote_work, flex_time, is_new, status) VALUES (:company_id, :title, :job_type, :salary_min, :salary_max, :location, :work_description, :job_change_scope, :requirements, :preferred, :benefits, :working_hours, :holiday, :remote_work, :flex_time, :is_new, :status)');
        $statement->execute($this->buildParams($data));
        $job_id = (int)$this->db->lastInsertId(); 
        $this->saveTags($job_id, $data['tags'] ?? ''); 
        return $job_id;
    }
    public function updateJob(int $id, array $data): void {
        $parameters = $this->buildParams($data); 
        $parameters[':id'] = $id;
        $this->db->prepare('UPDATE jobs SET company_id = :company_id, title = :title, job_type = :job_type, salary_min = :salary_min, salary_max = :salary_max, location = :location, work_description = :work_description, job_change_scope = :job_change_scope, requirements = :requirements, preferred = :preferred, benefits = :benefits, working_hours = :working_hours, holiday = :holiday, remote_work = :remote_work, flex_time = :flex_time, is_new = :is_new, status = :status WHERE id = :id')->execute($parameters);
        $this->db->prepare('DELETE FROM job_tags WHERE job_id=?')->execute([$id]);
        $this->saveTags($id, $data['tags'] ?? '');
    }
    public function deleteJob(int $id): void { $this->db->prepare('DELETE FROM jobs WHERE id=?')->execute([$id]); }
    public function toggleStatus(int $id): void { $this->db->prepare("UPDATE jobs SET status=IF(status='active','closed','active') WHERE id=?")->execute([$id]); }
    private function buildParams(array $data): array {
        return [
            ':company_id' => (int)($data['company_id'] ?? 0),
            ':title' => $data['title'] ?? '',
            ':job_type' => $data['job_type'] ?? '',
            ':salary_min' => $data['salary_min'] ?: null,
            ':salary_max' => $data['salary_max'] ?: null,
            ':location' => $data['location'] ?? '',
            ':work_description' => $data['work_description'] ?? '',
            ':job_change_scope' => $data['job_change_scope'] ?? '',
            ':requirements' => $data['requirements'] ?? '',
            ':preferred' => $data['preferred'] ?? '',
            ':benefits' => $data['benefits'] ?? '',
            ':working_hours' => $data['working_hours'] ?? '',
            ':holiday' => $data['holiday'] ?? '',
            ':remote_work' => isset($data['remote_work']) ? 1 : 0,
            ':flex_time' => isset($data['flex_time']) ? 1 : 0,
            ':is_new' => isset($data['is_new']) ? 1 : 0,
            ':status' => in_array($data['status'] ?? '', ['active', 'closed']) ? $data['status'] : 'active'
        ];
    }
    private function saveTags(int $job_id, string $raw_tags): void {
        $tags = array_filter(array_map('trim', explode(',', $raw_tags)));
        $statement = $this->db->prepare('INSERT INTO job_tags (job_id, tag) VALUES (?, ?)');
        foreach($tags as $tag) {
            $statement->execute([$job_id, $tag]);
        }
    }
}
