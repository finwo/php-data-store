<?php
include dirname(__DIR__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'autoload.php';
$path = explode('/',$_SERVER['REQUEST_URI']);
while(count($path)) {
    if ( is_file(__DIR__.implode(DIRECTORY_SEPARATOR,$path).'.php') ) {
        include(__DIR__.implode(DIRECTORY_SEPARATOR,$path).'.php');
        exit(0);
    }
    array_pop($path);
}

header('HTTP/1.0 404 Not Found');
echo 'Not Found';
