<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\ActiveTest as ActiveTest;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
$exams = (array) $user->activeTests ?? [];

if (!in_array($input["id"], $exams)){
    unset($exams);
    return false;
}
unset($exams);
$id = new ObjectId($input["id"]);

/*
 * Action
 */

$exam = new ActiveTest($db, $id);

// This piece is for backwards compatability with older test versions.
$solutions = $exam->solutions;
$results = $exam->results;
if (!isset($solutions) && isset($results)){
    $exam->solutions = $exam->results;
    $exam->results = null;
}

$response["msg"] = $dictionary->success;
$response["result"] = $exam->dump();
$response["code"] = "Success";

unset($id, $test);
