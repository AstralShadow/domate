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
$id = new ObjectId($input["id"]);

/*
 * Action
 */

$exercise = new Exercise($db, $id);
$response["msg"] = $dictionary->success;
$response["result"] = $exercise->dump();
$response["code"] = "Success";
