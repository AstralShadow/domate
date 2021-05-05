<?php

if (defined("FILTER_FUNCTIONS_INCLUDED")){
    return;
}
define("FILTER_FUNCTIONS_INCLUDED", true);

function scandirForPHP($dir) {
    return array_filter(scandir($dir), function ($string){
        if (!is_string($string)){
            return false;
        }
        if (substr($string, -4) != ".php"){
            return false;
        }
        if (strlen($string) < 4){
            return false;
        }
        return true;
    });
}

function scandirForDirectories($dir) {
    return array_filter(scandir($dir), function (string $item) use ($dir){
        if (in_array($item, ['.', '..'])){
            return false;
        }
        return is_dir($dir . '/' . $item);
    });
}

function filterAllowedOrDefault($input, array $allowed, $default, bool $strict = false) {
    if (in_array($input, $allowed, $strict)){
        return $input;
    }
    return $default;
}

function toFilenames(array $array) {
    foreach ($array as $key => $value){
        $array[$key] = pathinfo($value)["filename"];
    }
    return $array;
}
