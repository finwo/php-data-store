<?php

if (!defined('APPROOT')) define('APPROOT', dirname(__DIR__));
if (!defined('DS'))      define('DS'     , DIRECTORY_SEPARATOR);

// Simple PSR-0 autoloader
spl_autoload_register(function( $className ) {
    $path  = __DIR__ . DIRECTORY_SEPARATOR;
    $path .= str_replace("\\", DIRECTORY_SEPARATOR, $className);
    $path .= '.php';
    if ( file_exists($path) && is_readable($path) ) {
        include_once $path;
    }
});
