<?php

require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'autoload.php';

function url_params( $template ) {
    $data     = $_GET;
    $url      = $_SERVER['REQUEST_URI'];
    $url      = explode('?',$url);
    $url      = explode('/',array_shift($url));
    $template = explode('/',$template);
    foreach ( $template as $index => $name ) {
        if (substr($name,0,1)!=':') continue;
        $name = substr($name,1);
        $data[$name] = isset($url[$index]) ? $url[$index] : null;
    }
    return $data;
}

function get_deep( $data, $key = "" ) {
    if (is_string($key)) $key = explode('.', $key);
    if (!is_array($key)) return null;
    if ( is_object($data) ) $data = (array) $data;
    if ( !is_array($data) ) return null;
    if ( count($key) == 1 ) {
        $key = array_shift($key);
        if ( isset($data[$key]) ) {
            return $data[$key];
        }
        return null;
    }
    $current_key = array_shift($key);
    return get_deep( $data[$current_key], $key );
}

function entity_matches( $entity, $filter ) {
    foreach ( $filter as $key => $query ) {
        $value = get_deep( $entity, $key );
        switch(substr($query,0,1)) {
            case '/':
                if ( !preg_match($query, $value) ) return false;
                break;
            case '>':
                $query = substr($query,1);
                if ( !is_numeric($query) ) return false;
                if ( !is_numeric($value) ) return false;
                if (!(floatval($value) > floatval($query))) return false;
                break;
            case '<':
                $query = substr($query,1);
                if ( !is_numeric($query) ) return false;
                if ( !is_numeric($value) ) return false;
                if (!(floatval($value) < floatval($query))) return false;
                break;
            case '!':
                $query = substr($query,1);
                if ( $query == $value ) return false;
                break;
            default:
                if ( $query != $value ) return false;
                break;
        }
    }
    return true;
}

function random_char() {
    $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return $alphabet[rand(0, strlen($alphabet)-1)];
}

function uuid( $collection = null ) {
    if (is_null($collection)) return uniqid('_');
    $dir = APPROOT.DS.'data'.DS.$collection.DS;
    if (!is_dir($dir)) return uniqid('_');
    $output = '_';
    while ( strlen($output) < 5 )           $output .= random_char();
    while ( is_file($dir.$output.'.json') ) $output .= random_char();
    return $output;
}
