<?php
namespace App\Controllers;
use App\Models\ScreeningModel;
class ScreeningController extends BaseController {
    private ScreeningModel $model;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->model = new ScreeningModel();
    }

    public function show(int $id): void {
        $me = auth_user();
        $screening = $this->model->findById($id);

        if (!$screening) {
            $this->abort404();
        }

        if (!auth_check_role('admin') && (int)$screening['user_id'] !== (int)$me['id']) {
            $this->abort404();
        }

        $stepNames    = lang('selection');
        $statusLabels = lang('result_status');
        $overallLabels = lang('offer_status');
        $flash = $this->getFlash();

        $this->render('screening/show', compact('screening', 'stepNames', 'statusLabels', 'overallLabels', 'flash'));
    }

    public function updateStep(int $id): void {
        $this->verifyCsrf();
        $me = auth_user();
        $screening = $this->model->findById($id);

        if (!$screening) {
            $this->abort404();
        }

        if (!auth_check_role('admin') && (int)$screening['user_id'] !== (int)$me['id']) {
            $this->abort404();
        }

        $step = (int)$this->post('step', 0);
        if ($step < 0 || $step > 4) {
            $this->redirect('/screening/' . $id);
        }

        $this->model->upsertStep($id, $step, $_POST);

        if ($step === 4 && $this->post('step_status') === 'passed') {
            $this->model->updateOverallStatus($id, 'offered');
        }
        if ($this->post('step_status') === 'failed') {
            $this->model->updateOverallStatus($id, 'rejected');
        }

        $this->flash('ステップを更新しました。');
        $this->redirect('/screening/' . $id);
    }

    public function updateStatus(int $id): void {
        $this->verifyCsrf();
        $screening = $this->model->findById($id);
        if (!$screening) {
            $this->abort404();
        }
        if (!auth_check_role('admin') && (int)$screening['user_id'] !== (int)auth_user()['id']) {
            $this->abort404();
        }
        $this->model->updateOverallStatus($id, $this->post('overall_status', 'in_progress'));
        $this->flash('ステータスを更新しました。');
        $this->redirect('/screening/' . $id);
    }

    public function addFeedback(int $id): void {
        $this->verifyCsrf();
        $me = auth_user();
        $screening = $this->model->findById($id);

        if (!$screening) {
            $this->abort404();
        }
        if (!auth_check_role('admin') && (int)$screening['user_id'] !== (int)$me['id']) {
            $this->abort404();
        }

        $body = trim($this->post('body'));
        $step = $this->post('step') !== '' ? (int)$this->post('step') : null;
        if ($body !== '') {
            $this->model->addFeedback($id, $me['id'], $me['role'], $step, $body);
        }

        $this->flash('フィードバックを投稿しました。');
        $this->redirect('/screening/' . $id . '#feedbacks');
    }

    public function deleteFeedback(int $fid): void {
        $this->verifyCsrf();
        $me = auth_user();
        $screeningId = (int)$this->post('screening_id', 0);
        $this->model->deleteFeedback($fid, $me['id']);
        $this->redirect('/screening/' . $screeningId . '#feedbacks');
    }

    public function adminList(): void {
        $this->requireAdmin();
        
        $pager = $this->paginate($this->model->countAll());
        $screenings = $this->model->findAllPaged($pager['per_page'], $pager['offset']);

        $stepNames = lang('selection');
        $overallLabels = lang('offer_status');
        $flash = $this->getFlash();
        $this->render('admin/screenings/index', compact('screenings', 'stepNames', 'overallLabels', 'flash', 'pager'));
    }
}
