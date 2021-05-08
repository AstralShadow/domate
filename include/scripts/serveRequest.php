<?php

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

    if (in_array($_method, ["POST", "PUT", "DELETE"])){
        if (strpos($_SERVER["CONTENT_TYPE"], 'application/x-www-form-urlencoded') !== false){
            $_input = $_POST;
        }
        if (strpos($_SERVER["CONTENT_TYPE"], 'multipart/form-data') !== false){
            $_input = $_POST;
        }
        if (strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== false){
            try{
                $_input = json_decode(file_get_contents("php://input"), true);
            } catch (Exception $e){
                header("HTTP/1.1 400 Bad Request", true, 404);
                echo json_encode([
                    "code" => "400",
                    "note" => "Don't just pretend to, you need to actually send JSON."
                ]);
                die;
            }
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
