<?php

ini_set('date.timezone', 'UTC');

error_reporting(E_ALL ^ E_NOTICE);

define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));
define('APPLICATION_ENV', 'testing');
define('LIBRARY_PATH', realpath(__DIR__ . '/../library'));
define('VENDOR_PATH', realpath(__DIR__ . '/../vendor'));
define('TEST_PATH', realpath(__DIR__));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
  LIBRARY_PATH,
  TEST_PATH.'/library',
  VENDOR_PATH.'/zendframework/zendframework1/library',
  get_include_path()))
);
define('ORIG_INCLUDE_PATH', get_include_path());

register_shutdown_function(function() {
  @unlink('/tmp/log');
});

// Utility functions
require_once(APPLICATION_PATH . '/../library/Rexmac/Zyndax/functions.php');

// Autoloader
$composerAutoloader = require VENDOR_PATH . '/autoload.php';
$composerAutoloader->addClassMap(require_once APPLICATION_PATH . '/autoload_classmap.php');
$composerAutoloader->setUseIncludePath(true);
