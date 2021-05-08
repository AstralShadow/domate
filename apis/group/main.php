<?php

require "include/dictionary.php";
require "include/secureTokens.php";
require "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\ExerciseGroup as ExerciseGroup;

$_method;
$_path;
$_input;

if (count($_path) > 0){
    if ($_path[0] == "get-token"){
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        echo json_encode([
            "code" => "token",
            "token" => generateSecureToken($session, "group")
        ]);
        die;
    }

    $_id = $_path[0];

    $groups = (array) $user->exerciseGroups ?? [];
    if (!in_array($_id, $groups)){
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
        die;
    }
    if (!ExerciseGroup::exists($db, new ObjectId($_id))){
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
        die;
    }
    unset($tests);
}

if (!isset($_id) && $_method == "GET"){
    require "apis/group/list.php";
    return;
}

if (isset($_id) && $_method == "GET"){
    require "apis/group/get.php";
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
if (!verifySecureToken($session, "group", $_input["token"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "invalid_token",
        "message" => $dictionary["invalid_token"],
        "hint" => $dictionary["missing_token_hint"]
    ]);
    die;
}
unset($_input["token"]);

if (!isset($_id) && $_method == "POST"){
    if (count($_input) > 0){
        define("DONT_RETURN_CREATED_ID", 1);
    }

    require "apis/group/create.php";

    if (count($_input) > 0){
        require "apis/group/update.php";
    }

    return;
}

if (isset($_id) && $_method == "PUT"){
    require "apis/group/update.php";
    return;
}

if (isset($_id) && $_method == "DELETE"){
    require "apis/group/delete.php";
    return;
}


header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
echo json_encode([
    "code" => "404",
    "message" => $dictionary["404"]
]);
die;
