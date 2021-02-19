<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\Exercise as Exercise;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
$exercises = (array) $user->exercises ?? [];

if (!in_array($input["id"], $exercises)){
    return false;
}

$exercise = new Exercise($db, new ObjectId($input["id"]));
if (!$exercise || $exercise->owner !== $user->user){
    $response["msg"] = "You lack owhership here.";
    return;
}

/*
 * Action
 */

if (isset($input["name"]) && is_string($input["name"])){
    $exercise->name = $input["name"];
}

if (isset($input["description"]) && is_string($input["description"])){
    $exercise->description = $input["description"];
}


$response["msg"] = $dictionary->success;
// $response["result"] = $exercise->dump();
$response["code"] = "Success";

