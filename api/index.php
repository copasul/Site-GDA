<?php
$ROOT = realpath(__DIR__ . '/..');
chdir($ROOT);
set_include_path($ROOT . PATH_SEPARATOR . get_include_path());

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$uri = urldecode($uri);

// Se acessarem um .php específico (ex: /relatorio.php), executa ele
if (preg_match('#\.php$#i', $uri) && $uri !== '/api/index.php') {
    $target = realpath($ROOT . $uri);
    if ($target && str_starts_with($target, $ROOT) && is_file($target)) {
        require $target;
        exit;
    }
}

// Caso contrário, cai no index principal do sistema
require $ROOT . '/index.php';
