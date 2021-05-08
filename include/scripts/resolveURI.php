<?php

/*
 * Defines variable $request_ctx.
 * This variable is used by serveRequest script.
 */


require "include/functions/filterFunctions.php";

if (defined("resolvedURI")){
    return;
}
define("resolvedURI", true);

$requestURI = $_SERVER["REQUEST_URI"] ?? "";

$default = false;
if (!isset($requestURI)){
    $default = true;
} else if (!is_string($requestURI)){
    $default = true;
} else if (!preg_match("/^[a-zA-Z0-9\-\/]{2,}$/", $requestURI)){
    $default = true;
}

if ($default){
    $_requested_resource = [
        "method" => "GET",
        "api" => null,
        "page" => "home",
        "path" => []
    ];
    return;
}


//$pages = toFilenames(scandirForPHP("pages"));
$pages = ["app", "home"];
$apis = scandirForDirectories("apis");
$path = preg_split("/\//", $requestURI, -1, PREG_SPLIT_NO_EMPTY);
$req_base = strtolower($path[0]);

if (in_array($req_base, $apis, true)){
    $method_raw = strtoupper($_SERVER["REQUEST_METHOD"] ?? "GET");
    $method = filterAllowedOrDefault($method_raw, ["GET", "PUT", "POST", "DELETE"], "GET");

    $_requested_resource = [
        "method" => $method,
        "api" => $req_base,
        "path" => array_slice($path, 1)
    ];
    unset($pages, $apis, $path, $req_base, $method_raw, $method);
    return;
}
unset($apis);

if (in_array($req_base, $pages, true)){
    $_requested_resource = [
        "method" => "GET",
        "api" => null,
        "page" => $req_base,
        "path" => array_slice($path, 1) // TODO fix files' relative paths
    ];
    unset($pages, $path, $req_base);
    return;
}
unset($pages);

// page test
if (preg_match('/^[a-z0-9]{7}$/', $req_base)){
    $_requested_resource = [
        "method" => "GET",
        "api" => null,
        "page" => "test",
        "path" => array_slice($path, 1)
    ];
    $_key = $req_base;
    unset($path, $req_base);
    return;
}

unset($path, $req_base);

header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
die;
