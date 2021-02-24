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
    unset($exercises);
    return false;
}
unset($exercises);

$exercise = new Exercise($db, new ObjectId($input["id"]));
if (!$exercise || $exercise->owner !== $user->user){
    $response["msg"] = "You lack owhership here.";
    unset($exercise);
    return;
}

/*
 * Action
 */

if (isset($input["name"]) && is_string($input["name"])){
    $exercise->name = (string) $input["name"];
}

if (isset($input["description"]) && is_string($input["description"])){
    $exercise->description = (string) $input["description"];
}

if (isset($input["question"]) && is_string($input["question"])){
    $exercise->question = (string) $input["question"];
}

if (isset($input["answer"]) && is_string($input["answer"])){
    $exercise->answer = (string) $input["answer"];
}

if (isset($input["useAnswer"]) && is_bool($input["useAnswer"])){
    $exercise->useAnswer = (bool) $input["useAnswer"];
}

$response["msg"] = $dictionary->success;
$response["code"] = "Success";
unset($exercise);

