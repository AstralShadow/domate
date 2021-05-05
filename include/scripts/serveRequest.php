<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined("resolvedURI")){
    return;
}

$is_api = isset($_requested_resource["api"]);

if (!$is_api){
    require "include/dictionary.php";
    include "pages/" . $_requested_resource["page"] . ".php";

    unset($is_api);
    return;
}

if ($is_api){
    $_method = $_requested_resource["method"];
    $_path = $_requested_resource["path"];
    $_input = [];

    if (in_array($_method, ["POST", "PUT"])){
        if (strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== false){
            $_input = json_decode(file_get_contents("php://input"), true);
        } else
        if (strpos($_SERVER["CONTENT_TYPE"], 'application/x-www-form-urlencoded') !== false){
            $_input = $_POST;
        }
        if (strpos($_SERVER["CONTENT_TYPE"], 'multipart/form-data') !== false){
            $_input = $_POST;
        }
    }

    include "apis/" . $_requested_resource["api"] . "/main.php";

    unset($is_api, $_method, $_path, $_post);
    return;
}


header("HTTP/1.1 404 Not Found", true, 404);
echo json_encode([
    "code" => "404",
    "message" => $dictionary["404"]
]);

die;
