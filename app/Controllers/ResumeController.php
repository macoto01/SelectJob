<?php
namespace App\Controllers;
use App\Models\ResumeModel;

class ResumeController extends BaseController {
    private ResumeModel $model;
    private const SECTIONS  = ['basic','desired','skill','career','education','award','language','qualification'];
    private const PDF_VIEWS = ['resume'=>'resume/pdf_resume','career'=>'resume/pdf_career','career_en'=>'resume/pdf_career_en'];

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->model = new ResumeModel();
    }

    /** GET /resume - 自分の履歴書閲覧 */
    public function index(): void {
        $section = $this->validSection($this->get('section','basic'));
        $flash   = $this->getFlash();
        $data    = $this->model->getAllData();
        $this->render('resume/index', array_merge($data, compact('section','flash')));
    }

    /** GET /resume/edit - 自分の履歴書編集 */
    public function edit(): void {
        $section = $this->validSection($this->get('section','basic'));
        $data    = $this->model->getAllData();
        $this->render('resume/edit', array_merge($data, compact('section')));
    }

    /** POST /resume/save */
    public function save(): void {
        $this->verifyCsrf();
        $section = $this->validSection($this->post('section','basic'));
        $map = ['basic'=>'saveBasic','desired'=>'saveDesired','skill'=>'saveSkill','career'=>'saveCareer','education'=>'saveEducation','award'=>'saveAward','language'=>'saveLanguage','qualification'=>'saveQualification'];
        $this->model->{$map[$section]}($_POST);
        $this->flash('保存しました。');
        $this->redirect('/resume?section='.$section);
    }

    /** POST /resume/delete */
    public function delete(): void {
        $this->verifyCsrf();
        $section = $this->validSection($this->post('section',''));
        $id      = (int)$this->post('id',0);
        $map = ['career'=>'deleteCareer','education'=>'deleteEducation','award'=>'deleteAward','language'=>'deleteLanguage','qualification'=>'deleteQualification'];
        if($id>0&&isset($map[$section])) $this->model->{$map[$section]}($id);
        $this->redirect('/resume?section='.$section);
    }

    /** GET /resume/pdf */
    public function pdf(): void {
        $type = $this->get('type','resume');
        if(!isset(self::PDF_VIEWS[$type]))$this->abort404();
        $data = $this->model->getAllData();
        header('Content-Type: text/html; charset=UTF-8');
        $this->renderRaw(self::PDF_VIEWS[$type], $data);
    }

    /** GET /admin/resume/{userId} - 管理者がユーザーの履歴書を閲覧 */
    public function adminView(int $userId): void {
        $this->requireAdmin();
        // 対象ユーザー確認
        $targetUser = (new \App\Models\AuthModel())->findById($userId); // AuthModelを使用
        if (!$targetUser) $this->abort404(); // ユーザーが見つからない場合は404

        $section = $this->validSection($this->get('section','basic'));
        $flash   = $this->getFlash();
        $data    = $this->model->getAllDataByUserId($userId);
        $this->render('resume/admin_view', array_merge($data, compact('section','flash','targetUser')));
    }

    /** GET /admin/resume/{userId}/pdf - 管理者がPDF出力 */
    public function adminPdf(int $userId): void {
        $this->requireAdmin();
        $type = $this->get('type','resume');
        if(!isset(self::PDF_VIEWS[$type]))$this->abort404();
        $data = $this->model->getAllDataByUserId($userId);
        header('Content-Type: text/html; charset=UTF-8');
        $this->renderRaw(self::PDF_VIEWS[$type], $data);
    }

    private function validSection(string $s): string {
        return in_array($s, self::SECTIONS, true) ? $s : 'basic';
    }
}
