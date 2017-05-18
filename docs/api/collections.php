<?php
require_once 'init.php';
$list = array_values(array_map(function($entry) {
    return array('name'=>$entry);
}, array_filter(scandir(APPROOT.DS.'data'), function($entry) {
    return substr($entry,0,1) != '.';
})));

echo json_encode($list);
