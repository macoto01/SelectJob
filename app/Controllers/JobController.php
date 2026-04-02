<?php
// app/Controllers/JobController.php

class JobController {

    private JobModel $jobModel;
    private CompanyModel $companyModel;

    public function __construct() {
        $this->jobModel     = new JobModel();
        $this->companyModel = new CompanyModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** GET / または /jobs ―― 求人一覧 */
    public function index(): void {
        $filters = [
            'keyword'  => trim($_GET['keyword']  ?? ''),
            'location' => trim($_GET['location'] ?? ''),
            'remote'   => $_GET['remote'] ?? '',
            'flex'     => $_GET['flex']   ?? '',
            'sort'     => $_GET['sort']   ?? 'created_at DESC',
        ];

        $jobs  = $this->jobModel->findAll($filters);
        $total = count($jobs);

        $this->render('jobs/index', compact('jobs', 'total', 'filters'));
    }

    /** GET /jobs/{id} ―― 求人詳細 */
    public function show(int $id): void {
        $job = $this->jobModel->findById($id);
        if (!$job) {
            http_response_code(404);
            $this->render('shared/404');
            return;
        }
        $isApplied = $this->jobModel->isApplied($id);
        $this->render('jobs/show', compact('job', 'isApplied'));
    }

    /** POST /jobs/apply ―― 応募処理 */
    public function apply(): void {
        $jobId = (int)($_POST['job_id'] ?? 0);
        if ($jobId > 0 && !$this->jobModel->isApplied($jobId)) {
            $this->jobModel->apply($jobId);
        }
        // PRGパターン：一覧へリダイレクト
        $this->redirect('/jobs/' . $jobId);
    }

    // ----------------------------------------
    // 内部ヘルパー
    // ----------------------------------------

    private function render(string $view, array $data = []): void {
        extract($data);
        require BASE_PATH . '/app/Views/shared/header.php';
        require BASE_PATH . '/app/Views/' . $view . '.php';
        require BASE_PATH . '/app/Views/shared/footer.php';
    }

    private function redirect(string $path): void {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        header('Location: ' . $base . $path);
        exit;
    }
}
