<?php
/**
 * public/index.php  ─  エントリーポイント
 */
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
require dirname(__DIR__) . '/routes/web.php';
