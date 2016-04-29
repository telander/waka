<?php
//if (function_exists('xhprof_enable')) {
//    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
//}
$startTime = microtime(true);
error_reporting(E_ALL);
include __DIR__ . '/../framework/bootstrap.php';
Wk::createWebApp(include APP_ROOT . '/configs/config.php', $startTime)->run();
