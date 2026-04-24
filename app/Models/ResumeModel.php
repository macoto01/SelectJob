<?php
namespace App\Models;

class ResumeModel extends BaseModel {
    private function userId(): int {
        return (int)(auth_user()['id'] ?? 0);
    }

    public function getAllData(): array {
        return $this->getAllDataByUserId($this->userId());
    }

    public function getAllDataByUserId(int $uid): array {
        return [
            'basic'          => $this->getSection('resume_basic', $uid, true),
            'desired'        => $this->getSection('resume_desired', $uid),
            'skill'          => $this->getSection('resume_skill', $uid),
            'careers'        => $this->getList('resume_career', 'sort_order', $uid),
            'educations'     => $this->getList('resume_education', 'start_year DESC', $uid),
            'awards'         => $this->getList('resume_award', 'award_date DESC', $uid),
            'languages'      => $this->getList('resume_language', 'id', $uid),
            'qualifications' => $this->getList('resume_qualification', 'acquired_year DESC, acquired_month DESC', $uid),
            'prefs'          => lang('prefectures'),
        ];
    }

    private function getSection(string $tableName, int $userId, bool $isBasic = false): array {
        $stmt = $this->db->prepare("SELECT * FROM $tableName WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: [];
    }

    private function getList(string $tableName, string $order, int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM $tableName WHERE user_id = ? ORDER BY $order");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function saveBasic(array $profileInputs): void {
        $cols = ['last_name','first_name','last_name_kana','first_name_kana','gender','birthdate','phone','zip','address_pref','address_city','address_street','address_building','salary'];
        $this->upsertSection('resume_basic', $profileInputs, $cols);
    }

    public function saveDesired(array $desiredInputs): void {
        $this->upsertSection('resume_desired', $desiredInputs, ['desired_job','desired_location','desired_salary_min','desired_salary_max','desired_start','change_reason','appeal']);
    }

    public function saveSkill(array $skillInputs): void {
        $this->upsertSection('resume_skill', $skillInputs, ['it_skills','languages','certifications','other_skills']);
    }

    /**
     * 入力データからSQL実行用のパラメータを構築する（空文字はNULLに変換）
     */
    private function mapQueryParameters(array $inputs, array $columns): array {
        return array_combine(
            array_map(fn($col) => ":$col", $columns),
            array_map(fn($col) => (isset($inputs[$col]) && $inputs[$col] !== '') ? $inputs[$col] : null, $columns)
        );
    }

    private function upsertSection(string $tableName, array $formData, array $targetColumns): void {
        $uid = $this->userId();
        $ex  = $this->db->prepare("SELECT id FROM $tableName WHERE user_id = ?");
        $ex->execute([$uid]);
        $exists = $ex->fetch();

        $queryParameters = $this->mapQueryParameters($formData, $targetColumns);
        $queryParameters[':uid'] = $uid;

        if ($exists) {
            $updateStatements = implode(',', array_map(fn($col) => "$col = :$col", $targetColumns));
            $this->db->prepare("UPDATE $tableName SET $updateStatements WHERE user_id = :uid")->execute($queryParameters);
        } else {
            $columnNames       = implode(',', $targetColumns);
            $valuePlaceholders = ':' . implode(',:', $targetColumns);
            $this->db->prepare("INSERT INTO $tableName (user_id, $columnNames) VALUES (:uid, $valuePlaceholders)")->execute($queryParameters);
        }
    }

    public function saveCareer(array $careerInputs): void {
        $uid = $this->userId();
        $careerId = (int)($careerInputs['id'] ?? 0);
        $cols = [
            'company_name', 'employment_type', 'start_year', 'start_month', 
            'end_year', 'end_month', 'is_current', 'position', 'description', 
            'overseas_country', 'overseas_period', 'overseas_purpose', 'sort_order'
        ];
        
        $queryParameters = $this->mapQueryParameters($careerInputs, $cols);
        $queryParameters[':is_current'] = isset($careerInputs['is_current']) ? 1 : 0;
        $queryParameters[':uid']        = $uid;

        if ($careerId > 0) {
            $queryParameters[':id'] = $careerId;
            $sets = implode(',', array_map(fn($c) => "$c = :$c", $cols));
            $this->db->prepare("UPDATE resume_career SET $sets WHERE id = :id AND user_id = :uid")
            ->execute($queryParameters);
        } else {
            $cs = implode(',', $cols);
            $vs = ':' . implode(',:', $cols);
            $this->db->prepare("INSERT INTO resume_career (user_id, $cs) VALUES (:uid, $vs)")
            ->execute($queryParameters);
        }
    }

    public function saveEducation(array $educationInputs): void {
        $this->saveListRow('resume_education', $educationInputs, [
            'school_name', 'faculty', 'major', 'start_year', 'start_month', 
            'end_year', 'end_month', 'is_current', 'degree'
        ]);
    }

    public function saveAward(array $awardInputs): void {
        $this->saveListRow('resume_award', $awardInputs, ['title', 'organization', 'award_date', 'description']);
    }

    public function saveLanguage(array $languageInputs): void {
        $this->saveListRow('resume_language', $languageInputs, ['language', 'level', 'test_name', 'test_score']);
    }

    public function saveQualification(array $qualificationInputs): void {
        $this->saveListRow('resume_qualification', $qualificationInputs, ['name', 'acquired_date', 'in_progress']);
    }

    private function saveListRow(string $tableName, array $rowInputs, array $targetColumns): void {
        $uid = $this->userId();
        $rowId = (int)($rowInputs['id'] ?? 0);
        $queryParameters = $this->mapQueryParameters($rowInputs, $targetColumns);
        $queryParameters[':uid'] = $uid;

        if ($rowId > 0) {
            $queryParameters[':id'] = $rowId;
            $sets = implode(',', array_map(fn($c) => "$c = :$c", $targetColumns));
            $this->db->prepare("UPDATE $tableName SET $sets WHERE id = :id AND user_id = :uid")
            ->execute($queryParameters);
        } else {
            $columnNames       = implode(',', $targetColumns);
            $valuePlaceholders = ':' . implode(',:', $targetColumns);
            $this->db->prepare("INSERT INTO $tableName (user_id, $columnNames) VALUES (:uid, $valuePlaceholders)")
            ->execute($queryParameters);
        }
    }

    private function deleteListRow(string $tableName, int $id): void {
        $this->db->prepare("DELETE FROM $tableName WHERE id = ? AND user_id = ?")
            ->execute([$id, $this->userId()]);
    }

    public function findListRowById(string $tableName, int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM $tableName WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$id, $this->userId()]);
        return $stmt->fetch() ?: null;
    }

    public function deleteCareer(int $id): void { $this->deleteListRow('resume_career', $id); }
    public function deleteEducation(int $id): void { $this->deleteListRow('resume_education', $id); }
    public function deleteAward(int $id): void { $this->deleteListRow('resume_award', $id); }
    public function deleteLanguage(int $id): void { $this->deleteListRow('resume_language', $id); }
    public function deleteQualification(int $id): void { $this->deleteListRow('resume_qualification', $id); }
}
