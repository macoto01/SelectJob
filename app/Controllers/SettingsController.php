<?php
/**
 * app/Controllers/SettingsController.php
 * 設定・パスワード変更を担当
 */
namespace App\Controllers;
use App\Models\AuthModel;

class SettingsController extends BaseController {
    private AuthModel $auth;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->auth = new AuthModel();
    }

    /** GET /settings */
    public function index(): void {
        $flash = $this->getFlash();
        $this->render('settings/index', compact('flash'));
    }

    /** POST /settings/password - パスワード変更 */
    public function changePassword(): void {
        $this->verifyCsrf();
        $me      = auth_user();
        $current = $this->post('current_password');
        $new     = $this->post('new_password');
        $confirm = $this->post('new_password_confirm');

        if (!$this->auth->verifyPassword($me['id'], $current)) { // AuthModelのverifyPasswordを使用
            $this->flash('現在のパスワードが正しくありません。', 'error');
            $this->redirect('/settings');
        }
        if (strlen($new) < 6) {
            $this->flash('新しいパスワードは6文字以上で入力してください。', 'error');
            $this->redirect('/settings');
        }
        if ($new !== $confirm) {
            $this->flash('新しいパスワードが一致しません。', 'error');
            $this->redirect('/settings');
        }

        $this->auth->updatePassword($me['id'], $new); // AuthModelのupdatePasswordを使用

        $this->flash('パスワードを変更しました。');
        $this->redirect('/settings');
    }
}
