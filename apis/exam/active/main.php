<?php

require "include/dictionary.php";
include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\ActiveTest as ActiveTest;

$_method;
$_path;
$_input;
$_id;

if (count($_path) > 0){
    $_id2 = $_path[0];

    $tests = (array) $user->activeTests ?? [];
    if (!in_array($_id2, $tests)){
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
        die;
    }
    if (!ActiveTest::exists($db, new ObjectId($_id2))){
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
        die;
    }
    unset($tests);
}

if (!isset($_id2) && $_method == "GET"){
    require "apis/exam/active/list.php";
    return;
}

if (isset($_id2) && $_method == "GET"){
    require "apis/exam/active/get.php";
    return;
}

if (!isset($_id2) && $_method == "POST"){
    require "apis/exam/active/schedule.php";
    return;
}

if (isset($_id2) && $_method == "PUT"){
    header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented", true, 501);
    //require "apis/exam/active/update.php";
    return;
}

if (isset($_id2) && $_method == "DELETE"){
    require "apis/exam/active/delete.php";
    return;
}


header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
echo json_encode([
    "code" => "404",
    "message" => $dictionary["404"]
]);
die;
