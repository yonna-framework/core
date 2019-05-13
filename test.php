<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/test2.php';

$whoops = new Whoops\Run;
$handle = (new Whoops\Handler\PrettyPageHandler());
$handle->setPageTitle('phpure - core');
$whoops->pushHandler($handle);
$whoops->register();

dump('3333');

testDebug();