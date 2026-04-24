<?php
namespace App\Controllers;
use App\Models\AdminJobModel;
use App\Models\AuthModel;

class AdminController extends BaseController {
    private AdminJobModel $jobModel;
    private AuthModel     $authModel;
    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
        $this->jobModel  = new AdminJobModel();
        $this->authModel = new AuthModel();
    }
    // 求人管理
    public function index(): void {
        $keyword   = $this->get('keyword','');
        $status    = $this->get('status','');
        $companyId = (int)$this->get('company_id',0);
        $tab       = $this->get('tab','jobs');

        $pager = $this->paginate($this->jobModel->countJobs($keyword, $status, $companyId));

        $jobs         = $this->jobModel->searchJobs($keyword, $status, $companyId, $pager['per_page'], $pager['offset']);
        $companies    = $this->jobModel->searchCompanies($tab==='companies' ? $keyword : '', $pager['per_page'], $pager['offset']);

        $allCompanies = $this->jobModel->getAllCompanies();
        $flash        = $this->getFlash();
        $totalActive  = count(array_filter($jobs,fn($j)=>$j['status']==='active'));
        $totalClosed  = count(array_filter($jobs,fn($j)=>$j['status']==='closed'));
        $this->render('admin/jobs/index', compact('jobs','companies','allCompanies','flash','keyword','status','companyId','tab','totalActive','totalClosed', 'pager'));
    }

    public function createJobForm(): void {
        $companies = $this->jobModel->getAllCompanies();
        $flash = $this->getFlash();
        $this->render('admin/jobs/form', compact('companies', 'flash'));
    }

    public function storeJob(): void {
        $this->verifyCsrf();
        $id = $this->jobModel->createJob($_POST);
        $this->flash($this->lang('success.job_created'));
        $this->redirect('/admin/jobs/' . $id . '/edit');
    }

    public function editJobForm(int $id): void {
        $job = $this->jobModel->findJobById($id);
        if (!$job) $this->abort404();
        $companies = $this->jobModel->getAllCompanies();
        $flash = $this->getFlash();
        $this->render('admin/jobs/form', compact('job', 'companies', 'flash'));
    }

    public function updateJob(int $id): void {
        $this->verifyCsrf();
        $this->jobModel->updateJob($id, $_POST);
        $this->flash($this->lang('success.job_updated'));
        $this->redirect('/admin/jobs/' . $id . '/edit');
    }

    public function deleteJob(int $id): void {
        $this->verifyCsrf();
        $this->jobModel->deleteJob($id);
        $this->flash($this->lang('success.job_deleted'), 'error');
        $this->redirect('/admin');
    }

    public function toggleJob(int $id): void {
        $this->verifyCsrf();
        $this->jobModel->toggleStatus($id);
        $this->redirect('/admin');
    }

    // 会社管理
    public function createCompanyForm(): void {
        $flash = $this->getFlash();
        $this->render('admin/companies/form', compact('flash'));
    }

    public function storeCompany(): void {
        $this->verifyCsrf();
        $id = $this->jobModel->createCompany($_POST);
        $this->flash($this->lang('success.company_created'));
        $this->redirect('/admin/companies/' . $id . '/edit');
    }

    public function editCompanyForm(int $id): void {
        $company = $this->jobModel->findCompanyById($id);
        if (!$company) $this->abort404();
        $flash = $this->getFlash();
        $this->render('admin/companies/form', compact('company', 'flash'));
    }

    public function updateCompany(int $id): void {
        $this->verifyCsrf();
        $this->jobModel->updateCompany($id, $_POST);
        $this->flash($this->lang('success.company_updated'));
        $this->redirect('/admin/companies/' . $id . '/edit');
    }

    // ユーザー管理
    public function userList(): void {
        $pager = $this->paginate($this->authModel->countAll());
        $users = $this->authModel->findPaged($pager['per_page'], $pager['offset']);
        
        $flash = $this->getFlash();
        $this->render('admin/users/index', compact('users', 'flash', 'pager'));
    }

    public function userCreate(): void {
        $flash = $this->getFlash();
        $this->render('admin/users/form', compact('flash'));
    }

    public function userStore(): void {
        $this->verifyCsrf();
        if($this->post('name')===''||$this->post('email')===''||$this->post('password')===''){$this->flash($this->lang('error.required_full'),'error');$this->redirect('/admin/users/create');}
        if($this->authModel->emailExists($this->post('email'))){$this->flash($this->lang('error.email_duplicate'),'error');$this->redirect('/admin/users/create');}
        $id=$this->authModel->create($_POST); $this->flash($this->lang('success.user_created')); $this->redirect('/admin/users/'.$id);
    }

    public function userShow(int $id): void {
        $user = $this->authModel->findDetailById($id);
        if (!$user) $this->abort404();
        $flash = $this->getFlash();
        $this->render('admin/users/show', compact('user', 'flash'));
    }

    public function userEdit(int $id): void {
        $user = $this->authModel->findDetailById($id);
        if (!$user) $this->abort404();
        $flash = $this->getFlash();
        $this->render('admin/users/form', compact('user', 'flash'));
    }

    public function userUpdate(int $id): void {
        $this->verifyCsrf();
        if($this->post('name')===''||$this->post('email')===''){$this->flash($this->lang('error.required_basic'),'error');$this->redirect('/admin/users/'.$id.'/edit');}
        if($this->authModel->emailExists($this->post('email'),$id)){$this->flash($this->lang('error.email_duplicate'),'error');$this->redirect('/admin/users/'.$id.'/edit');}
        $this->authModel->update($id,$_POST); $this->flash($this->lang('success.user_updated')); $this->redirect('/admin/users/'.$id);
    }

    public function userToggle(int $id): void {
        $this->verifyCsrf();
        $this->authModel->toggleActive($id);
        $this->flash($this->lang('success.status_updated'));
        $this->redirect('/admin/users/' . $id);
    }
}
