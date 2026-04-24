<?php
/**
 * routes/web.php  ─  フロントコントローラー兼ルーター
 */

use App\Controllers\AuthController;
use App\Controllers\JobController;
use App\Controllers\ResumeController;
use App\Controllers\AdminController;
use App\Controllers\ChatController;
use App\Controllers\ScreeningController;
use App\Controllers\SettingsController;

define('BASE_PATH', dirname(__DIR__));

// オートローダーの読み込み（index.php等で読み込まれていない場合、ここで必要になります）
require_once BASE_PATH . '/vendor/autoload.php';

require BASE_PATH . '/config/database.php'; // DB接続ヘルパー
require BASE_PATH . '/app/Helpers/helpers.php'; // グローバルヘルパー関数

$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', \PHP_URL_PATH);
$base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$path   = '/' . ltrim(substr($uri, strlen($base)), '/');
$method = $_SERVER['REQUEST_METHOD'];

// ルーティング定義
$routes = [
    // 認証関連
    ['GET',  '#^/login$#',                 AuthController::class, 'loginForm'],
    ['POST', '#^/login$#',                 AuthController::class, 'loginPost'],
    ['GET',  '#^/logout$#',                AuthController::class, 'logout'],
    ['GET',  '#^/forgot-password$#',       AuthController::class, 'forgotPasswordForm'],
    ['POST', '#^/forgot-password$#',       AuthController::class, 'forgotPasswordPost'],
    ['GET',  '#^/reset-password$#',        AuthController::class, 'resetPasswordForm'],
    ['POST', '#^/reset-password$#',        AuthController::class, 'resetPasswordPost'],

    // ユーザー向け求人・選考・チャット・履歴書
    ['GET',  '#^/$#',                      JobController::class, 'home'],
    ['GET',  '#^/jobs$#',                  JobController::class, 'index'],
    ['GET',  '#^/jobs/(\d+)$#',            JobController::class, 'show'],
    ['POST', '#^/jobs/apply$#',            JobController::class, 'apply'],

    ['GET',  '#^/screening/(\d+)$#',       ScreeningController::class, 'show'],
    ['POST', '#^/screening/(\d+)/step$#',  ScreeningController::class, 'updateStep'],
    ['POST', '#^/screening/(\d+)/status$#',ScreeningController::class, 'updateStatus'],
    ['POST', '#^/screening/(\d+)/feedback$#',ScreeningController::class, 'addFeedback'],
    ['POST', '#^/screening/feedback/(\d+)/delete$#',ScreeningController::class, 'deleteFeedback'],

    ['GET',  '#^/chat$#',                  ChatController::class, 'userRoom'],
    ['POST', '#^/chat/send$#',             ChatController::class, 'userSend'],
    ['GET',  '#^/chat/poll$#',             ChatController::class, 'userPoll'],

    ['GET',  '#^/resume$#',                ResumeController::class, 'index'],
    ['GET',  '#^/resume/edit$#',           ResumeController::class, 'edit'],
    ['POST', '#^/resume/save$#',           ResumeController::class, 'save'],
    ['POST', '#^/resume/delete$#',         ResumeController::class, 'delete'],
    ['GET',  '#^/resume/pdf$#',            ResumeController::class, 'pdf'],

    // ユーザー設定
    ['GET',  '#^/settings$#',              SettingsController::class, 'index'],
    ['POST', '#^/settings/password$#',     SettingsController::class, 'changePassword'],

    // 管理者向け機能
    ['GET',  '#^/admin$#',                         AdminController::class, 'index'],
    ['GET',  '#^/admin/jobs/create$#',             AdminController::class, 'createJobForm'],
    ['POST', '#^/admin/jobs/store$#',              AdminController::class, 'storeJob'],
    ['GET',  '#^/admin/jobs/(\d+)/edit$#',         AdminController::class, 'editJobForm'],
    ['POST', '#^/admin/jobs/(\d+)/update$#',       AdminController::class, 'updateJob'],
    ['POST', '#^/admin/jobs/(\d+)/delete$#',       AdminController::class, 'deleteJob'],
    ['POST', '#^/admin/jobs/(\d+)/toggle$#',       AdminController::class, 'toggleJob'],

    ['GET',  '#^/admin/companies/create$#',        AdminController::class, 'createCompanyForm'],
    ['POST', '#^/admin/companies/store$#',         AdminController::class, 'storeCompany'],
    ['GET',  '#^/admin/companies/(\d+)/edit$#',    AdminController::class, 'editCompanyForm'],
    ['POST', '#^/admin/companies/(\d+)/update$#',  AdminController::class, 'updateCompany'],

    ['GET',  '#^/admin/users$#',                   AdminController::class, 'userList'],
    ['GET',  '#^/admin/users/create$#',            AdminController::class, 'userCreate'],
    ['POST', '#^/admin/users/store$#',             AdminController::class, 'userStore'],
    ['GET',  '#^/admin/users/(\d+)$#',             AdminController::class, 'userShow'],
    ['GET',  '#^/admin/users/(\d+)/edit$#',        AdminController::class, 'userEdit'],
    ['POST', '#^/admin/users/(\d+)/update$#',      AdminController::class, 'userUpdate'],
    ['POST', '#^/admin/users/(\d+)/toggle$#',      AdminController::class, 'userToggle'],

    ['GET',  '#^/admin/screenings$#',              ScreeningController::class, 'adminList'],

    ['GET',  '#^/admin/chat$#',                    ChatController::class, 'adminList'],
    ['GET',  '#^/admin/chat/(\d+)$#',              ChatController::class, 'adminRoom'],
    ['POST', '#^/admin/chat/(\d+)/send$#',         ChatController::class, 'adminSend'],
    ['GET',  '#^/admin/chat/(\d+)/poll$#',         ChatController::class, 'adminPoll'],

    // 管理者：ユーザー履歴書閲覧
    ['GET',  '#^/admin/resume/(\d+)$#',            ResumeController::class, 'adminView'],
    ['GET',  '#^/admin/resume/(\d+)/pdf$#',        ResumeController::class, 'adminPdf'],
];

foreach ($routes as [$routeMethod, $pattern, $pathClass, $action]) {
    if ($method !== $routeMethod) continue;
    if (!preg_match($pattern, $path, $matches)) continue;
    $args = array_map('intval', array_slice($matches, 1));
    (new $pathClass())->$action(...$args); // $classを$pathClassに修正
    exit;
}

// 404 Not Found 処理
http_response_code(404);
require BASE_PATH . '/app/Views/shared/header.php';
require BASE_PATH . '/app/Views/shared/404.php';
require BASE_PATH . '/app/Views/shared/footer.php';
