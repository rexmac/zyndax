<?php

date_default_timezone_set('UTC');

if(!function_exists('array_sort_by_key')) {
  function array_sort_by_key($array, $key, $order = SORT_ASC) {
    if(!is_array($array)) throw new Exception('First parameter must be an array');
    $newArray = array();
    if(count($array) <= 0) return $newArray;
    $sortableArray = array();
    foreach($array as $k => $v) {
      if(is_array($v)) {
        foreach($v as $k2 => $v2) {
          if($k2 === $key) $sortableArray[$k] = $v2;
        }
      } else {
        $sortableArray[$k] = $v;
      }
    }

    switch($order) {
      case SORT_ASC: asort($sortableArray); break;
      case SORT_DESC: arsort($sortableArray); break;
    }

    foreach($sortableArray as $k => $v) {
      $newArray[$k] = $array[$k];
    }

    return $newArray;
  }
}

if(!function_exists('mb_ucfirst')) {
  function mb_ucfirst($str, $encoding = 'UTF-8', $lowerStrEnd = false) {
    $firstLetter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
    $strEnd = '';
    if($lowerStrEnd) {
      $strEnd = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
    } else {
      $strEnd = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
    $str = $firstLetter . $strEnd;
    return $str;
  }
}

if(!function_exists('sanitize_string_for_xml_tag')) {
  function sanitize_string_for_xml_tag($string) {
    return preg_replace('/[^:_A-Za-z\x{C0}-\x{D6}\x{D8}-\x{F6}\x{F8}-\x{2FF}\x{370}-\x{37D}\x{37F}-\x{1FFF}\x{200C}-\x{200D}\x{2070}-\x{218F}\x{2C00}-\x{2FEF}\x{3001}-\x{D7FF}\x{F900}-\x{FDCF}\x{10000}-\x{EFFFF}]/u', '', $string);
  }
}

if(!function_exists('url_exists')) {
  function url_exists($url) {
    $hdrs = @get_headers($url);
    return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $hdrs[0]) : false;
  }
}

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
  realpath(APPLICATION_PATH . '/../library'),
  get_include_path(),
)));

register_shutdown_function('session_write_close');

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
  APPLICATION_ENV,
  APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();
