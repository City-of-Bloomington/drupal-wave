<?php
$_SERVER['SITE_HOME'] = __DIR__.'/data';
define('APPLICATION_HOME', realpath(__DIR__.'/../../'));
define('VERSION', trim(file_get_contents(APPLICATION_HOME.'/VERSION')));
define('SITE_HOME', $_SERVER['SITE_HOME']);

$loader = require APPLICATION_HOME.'/vendor/autoload.php';
$loader->addPsr4('Site\\', SITE_HOME);

include SITE_HOME.'/site_config.php';
include APPLICATION_HOME.'/src/Web/routes.php';
include APPLICATION_HOME.'/src/Web/access_control.php';
