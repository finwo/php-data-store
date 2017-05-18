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
        header('Access-Control-Allow-Origin: *');

        if (!isset($params['id'])) echo '[';
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
                    echo json_encode($entity);
                    exit(0);
                }
                continue;
            }
            $entity        = json_decode(file_get_contents($dir.$file), true);
            $entity['_id'] = $id;
            if ( isset($params['filter']) && !entity_matches($entity,$params['filter']) ) {
                continue;
            }
            echo $sep;
            $sep = ',';
            echo json_encode($entity);
        }
        if (!isset($params['id'])) echo ']';
        break;

    case 'POST':
        $dir = APPROOT.DS.'data'.DS.$params['collection'].DS;
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $id   = isset($params['id']) ? $params['id'] : uuid($params['collection']);
        $file = $dir.$id.'.json';
        file_put_contents($file,json_encode($_POST));
        $_POST['_id'] = $id;
        echo json_encode($_POST);

        break;

    case 'DELETE':
        $dir = APPROOT.DS.'data'.DS.$params['collection'].DS;
        if (!is_dir($dir)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Not Found - Collection does not exist';
            exit(0);
        }
        if (!isset($params['id'])) {
            header('HTTP/1.0 400 Not Found');
            echo 'Bad Request - No ID given';
            exit(0);
        }

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $file = $dir.$params['id'].'.json';
        if(!is_file($file)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Not Found - Entity does not exist';
            exit(0);
        }

        $entity        = json_decode(file_get_contents($file), true);
        $entity['_id'] = $params['id'];
        unlink($file);
        echo json_encode($entity);
        break;

    case 'OPTIONS':
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE');
        if (array_key_exists('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', $_SERVER)) {
            header('Access-Control-Allow-Headers: '.$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
        } else {
            header('Access-Control-Allow-Headers: *');
        }
        break;

    default:
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad Request - Invalid method';
        break;
}
