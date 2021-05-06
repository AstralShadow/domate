<?php

require "include/dictionary.php";
require "include/secureTokens.php";

use MongoDB\BSON\ObjectId;
use MathExam\Test as Test;

$_method;
$_path;
$_input;

if (count($_path) > 0){
    if ($_path[0] == "get-token"){
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        echo json_encode([
            "code" => "token",
            "token" => generateSecureToken($session, "test")
        ]);
        die;
    }

    $_id = $_path[0];

    $tests = (array) $user->tests ?? [];
    if (!in_array($_id, $tests)){
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
        die;
    }
    if (!Test::exists($db, new ObjectId($_id))){
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
        die;
    }
    unset($tests);
}

if (!isset($_id) && $_method == "GET"){
    require "apis/exam/list.php";
    return;
}

if (isset($_id) && $_method == "GET"){
    require "apis/exam/get.php";
    return;
}

if (isset($_id) && count($_path) > 1 && $_path[1] == "active" && $_method == "GET"){
    $_path = array_slice($_path, 2);
    require "apis/exam/active/main.php";
    return;
}

if (!isset($_input["token"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "missing_token",
        "message" => $dictionary["missing_token"],
        "hint" => $dictionary["missing_token_hint"]
    ]);
    die;
}
if (!verifySecureToken($session, "test", $_input["token"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "invalid_token",
        "message" => $dictionary["invalid_token"],
        "hint" => $dictionary["missing_token_hint"]
    ]);
    die;
}
unset($_input["token"]);

if (isset($_id) && count($_path) > 1 && $_path[1] == "active"){
    $_path = array_slice($_path, 2);
    require "apis/exam/active/main.php";
    return;
}

if (!isset($_id) && $_method == "POST"){
    if (count($_input) > 0){
        define("DONT_RETURN_CREATED_ID", 1);
    }

    require "apis/exam/create.php";

    if (count($_input) > 0){
        require "apis/exam/update.php";
    }

    return;
}

if (isset($_id) && $_method == "PUT"){
    require "apis/exam/update.php";
    return;
}

if (isset($_id) && $_method == "DELETE"){
    require "apis/exam/delete.php";
    return;
}


/* 500 */
header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
echo json_encode([
    "code" => "500",
    "message" => $dictionary["500"]
]);
die;
