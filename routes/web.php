<?php
// routes/web.php  ―  シンプルルーター

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/config/database.php';
require BASE_PATH . '/app/Models/JobModel.php';
require BASE_PATH . '/app/Models/CompanyModel.php';
require BASE_PATH . '/app/Controllers/JobController.php';

// URLパス取得（クエリ文字列を除いた純粋なパス）
$requestUri  = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName  = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath    = rtrim(dirname($scriptName), '/');
$path        = '/' . ltrim(substr(parse_url($requestUri, PHP_URL_PATH), strlen($basePath)), '/');

$controller = new JobController();

// ルーティング
if ($path === '/' || $path === '/jobs') {
    $controller->index();
} elseif (preg_match('#^/jobs/(\d+)$#', $path, $m)) {
    $controller->show((int)$m[1]);
} elseif ($path === '/jobs/apply' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->apply();
} else {
    http_response_code(404);
    require BASE_PATH . '/app/Views/shared/404.php';
}
