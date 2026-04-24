<?php
namespace App\Controllers;
use App\Models\JobModel;
use App\Models\CompanyModel;
use App\Models\ScreeningModel;

class JobController extends BaseController {
    private JobModel $jobModel;
    private CompanyModel $companyModel;
    private ScreeningModel $screeningModel;
    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->jobModel       = new JobModel();
        $this->companyModel   = new CompanyModel();
        $this->screeningModel = new ScreeningModel();
    }
    public function home(): void {
        $me            = auth_user();
        $screenings    = $this->screeningModel->findByUser($me['id']);
        $stepNames     = lang('selection');
        $overallLabels = lang('offer_status');
        $statusLabels  = lang('result_status');
        $this->render('jobs/home', compact('screenings','stepNames','overallLabels','statusLabels'));
    }
    public function index(): void {
        $filters = $this->buildFilters();
        $jobs    = $this->jobModel->findAll($filters);
        $total   = count($jobs);
        $this->render('jobs/index', compact('jobs','total','filters'));
    }
    public function show(int $id): void {
        $job = $this->jobModel->findById($id);
        if (!$job) $this->abort404();
        $isApplied = $this->jobModel->isApplied($id);
        $this->render('jobs/show', compact('job','isApplied'));
    }
    public function apply(): void {
        $this->verifyCsrf();
        $me    = auth_user();
        $jobId = (int)$this->post('job_id', 0);
        if ($jobId > 0 && !$this->jobModel->isApplied($jobId)) {
            $this->jobModel->apply($jobId);
            $this->screeningModel->createFromApplication($me['id'], $jobId);
        }
        $this->redirect('/jobs/' . $jobId);
    }
    private function buildFilters(): array {
        $allowed = ['created_at DESC','salary_max DESC','created_at ASC'];
        $sort = $this->get('sort','created_at DESC');
        return ['keyword'=>$this->get('keyword'),'location'=>$this->get('location'),'remote'=>$this->get('remote'),'flex'=>$this->get('flex'),'sort'=>in_array($sort,$allowed,true)?$sort:'created_at DESC'];
    }
}
