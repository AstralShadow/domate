<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\Test as Test;
use MathExam\ExerciseGroup as ExerciseGroup;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
$tests = (array) $user->tests ?? [];

if (!in_array($input["id"], $tests)){
    return false;
}
$id = new ObjectId($input["id"]);

/*
 * Action
 */

$success = Test::remove($db, $user, $id);

if (!$success){
    return false;
}

$response["msg"] = $dictionary->success;
$response["code"] = "Success";
