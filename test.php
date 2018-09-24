<?php
//We loads autoloads
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/autoload.php';

$autoloadApp = new app\AutoloadApp();
$autoloadApp->register();

$GLOBALS['config'] = $config = require __DIR__.'/config/config.php';
