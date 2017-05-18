<?php
require_once 'init.php';
$method = $_SERVER['REQUEST_METHOD'];
$params = url_params("/api/data/:collection/:id");
if(is_null($params['collection'])) {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad Request - No collection given';
    exit(0);
}

switch($method) {
    case 'GET':
        $dir = APPROOT.DS.'data'.DS.$params['collection'].DS;
        if (!is_dir($dir)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Not Found - Collection does not exist';
            exit(0);
        }

        header('Content-Type: application/json');
        if (!isset($params['id'])) print('[');
        $dh  = opendir($dir);
        $sep = '';
        while( $file = readdir($dh) ) {
            if ( substr($file,0,1) == '.' ) continue;
            $id  = explode('.',$file);
            $ext = array_pop($id);
            $id  = implode('.',$id);
            if ( $ext != 'json' ) continue;
            if ( isset($params['id']) ) {
                if ( $params['id'] == $id ) {
                    $entity        = json_decode(file_get_contents($dir.$file), true);
                    $entity['_id'] = $id;
                    print(json_encode($entity));
                    exit(0);
                }
                continue;
            }
            $entity        = json_decode(file_get_contents($dir.$file), true);
            $entity['_id'] = $id;
            if ( isset($params['filter']) && !entity_matches($entity,$params['filter']) ) {
                continue;
            }
            print($sep);
            $sep = ',';
            print(json_encode($entity));
        }
        if (!isset($params['id'])) print(']');
        break;

    case 'POST':
        $dir = APPROOT.DS.'data'.DS.$params['collection'].DS;
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $id   = uuid($params['collection']);
        $file = $dir.$id.'.json';
        file_put_contents($file,json_encode($_POST));
        $_POST['_id'] = $id;
        print(json_encode($_POST));

        break;
}
