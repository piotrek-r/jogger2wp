<?php

date_default_timezone_set('Europe/Warsaw');

if('cli' !== PHP_SAPI) {
    echo 'This is intended to be run as CLI command', PHP_EOL;
    exit(1);
}

spl_autoload_register(function($name) {
    $parts = explode('\\', $name);
    array_unshift($parts, __DIR__, 'classes');
    $path = implode(DIRECTORY_SEPARATOR, $parts) . '.php';
    require_once $path;
});

$j2w = new Jogger2Wp\Application($argv);
exit($j2w->run());
