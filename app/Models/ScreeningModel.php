<?php
namespace App\Models;

class ScreeningModel extends BaseModel {
    public function findByUser(int $userId): array {
        $stmt=$this->db->prepare('SELECT s.*,j.title AS job_title,c.name AS company_name,j.location AS job_location FROM screenings s JOIN jobs j ON j.id=s.job_id JOIN companies c ON c.id=j.company_id WHERE s.user_id=:uid ORDER BY s.updated_at DESC');
        $stmt->execute([':uid'=>$userId]); $rows=$stmt->fetchAll();
        foreach($rows as &$row) $row['steps']=$this->getSteps($row['id']);
        return $rows;
    }
    public function findAllPaged(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT s.*,u.name AS user_name,j.title AS job_title,c.name AS company_name FROM screenings s JOIN users u ON u.id=s.user_id JOIN jobs j ON j.id=s.job_id JOIN companies c ON c.id=j.company_id ORDER BY s.updated_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function countAll(): int {
        return (int)$this->db->query('SELECT COUNT(*) FROM screenings')->fetchColumn();
    }
    public function findById(int $id): ?array {
        $stmt=$this->db->prepare('SELECT s.*,u.name AS user_name,u.email AS user_email,j.title AS job_title,c.name AS company_name,c.website AS company_website FROM screenings s JOIN users u ON u.id=s.user_id JOIN jobs j ON j.id=s.job_id JOIN companies c ON c.id=j.company_id WHERE s.id=:id LIMIT 1');
        $stmt->execute([':id'=>$id]); $row=$stmt->fetch(); if(!$row) return null;
        $row['steps']=$this->getSteps($id); $row['feedbacks']=$this->getFeedbacks($id); return $row;
    }
    public function belongsToUser(int $screeningId, int $userId): bool {
        $stmt=$this->db->prepare('SELECT COUNT(*) FROM screenings WHERE id=? AND user_id=?'); $stmt->execute([$screeningId,$userId]); return (int)$stmt->fetchColumn()>0;
    }
    public function createFromApplication(int $userId, int $jobId): int {
        $stmt=$this->db->prepare('SELECT id FROM screenings WHERE user_id=? AND job_id=? LIMIT 1'); $stmt->execute([$userId,$jobId]); $ex=$stmt->fetch();
        if($ex) return (int)$ex['id'];
        $this->db->prepare('INSERT INTO screenings (user_id,job_id,current_step) VALUES (?,?,0)')->execute([$userId,$jobId]);
        $id=(int)$this->db->lastInsertId(); $this->upsertStep($id,0,['step_status'=>'pending']); return $id;
    }
    public function updateOverallStatus(int $id, string $status): void {
        if(!in_array($status,['in_progress','offered','rejected','withdrawn'],true)) return;
        $this->db->prepare('UPDATE screenings SET overall_status=:s,updated_at=NOW() WHERE id=:id')->execute([':s'=>$status,':id'=>$id]);
    }
    public function getSteps(int $screeningId): array {
        $stmt=$this->db->prepare('SELECT * FROM screening_steps WHERE screening_id=? ORDER BY step'); $stmt->execute([$screeningId]);
        $indexed=[]; foreach($stmt->fetchAll() as $r) $indexed[(int)$r['step']]=$r; return $indexed;
    }
    public function upsertStep(int $screeningId, int $step, array $d): void {
        $ex=$this->db->prepare('SELECT id FROM screening_steps WHERE screening_id=? AND step=? LIMIT 1'); $ex->execute([$screeningId,$step]); $row=$ex->fetch();
        $status=$d['step_status']??'pending';
        $sa=!empty($d['scheduled_at'])?$d['scheduled_at']:null;
        $mu=!empty($d['meet_url'])?$d['meet_url']:null;
        $ln=!empty($d['location_note'])?$d['location_note']:null;
        $ra=in_array($status,['passed','failed','cancelled'])?date('Y-m-d H:i:s'):null;
        if($row){
            $this->db->prepare('UPDATE screening_steps SET step_status=:ss,scheduled_at=:sa,meet_url=:mu,location_note=:ln,result_at=:ra,updated_at=NOW() WHERE id=:id')
                ->execute([':ss'=>$status,':sa'=>$sa,':mu'=>$mu,':ln'=>$ln,':ra'=>$ra,':id'=>$row['id']]);
        } else {
            $this->db->prepare('INSERT INTO screening_steps (screening_id,step,step_status,scheduled_at,meet_url,location_note,result_at) VALUES (:sid,:step,:ss,:sa,:mu,:ln,:ra)')
                ->execute([':sid'=>$screeningId,':step'=>$step,':ss'=>$status,':sa'=>$sa,':mu'=>$mu,':ln'=>$ln,':ra'=>$ra]);
        }
        $this->syncCurrentStep($screeningId);
    }
    private function syncCurrentStep(int $screeningId): void {
        $steps=$this->getSteps($screeningId); $current=0;
        foreach($steps as $n=>$s) if($s['step_status']==='passed') $current=$n+1;
        $this->db->prepare('UPDATE screenings SET current_step=:c,updated_at=NOW() WHERE id=:id')->execute([':c'=>$current,':id'=>$screeningId]);
    }
    public function getFeedbacks(int $screeningId): array {
        $stmt=$this->db->prepare('SELECT f.*,u.name AS author_name FROM screening_feedbacks f JOIN users u ON u.id=f.author_id WHERE f.screening_id=:sid ORDER BY f.created_at ASC');
        $stmt->execute([':sid'=>$screeningId]); return $stmt->fetchAll();
    }
    public function addFeedback(int $screeningId, int $authorId, string $role, ?int $step, string $body): void {
        $this->db->prepare('INSERT INTO screening_feedbacks (screening_id,step,author_id,author_role,body) VALUES (:sid,:step,:aid,:role,:body)')
            ->execute([':sid'=>$screeningId,':step'=>$step,':aid'=>$authorId,':role'=>$role,':body'=>$body]);
    }
    public function deleteFeedback(int $feedbackId, int $authorId): void {
        $this->db->prepare('DELETE FROM screening_feedbacks WHERE id=? AND author_id=?')->execute([$feedbackId,$authorId]);
    }
}
