<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\TestSolution as TestSolution;
use MathExam\ActiveTest as ActiveTest;

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
if (!TestSolution::exists($db, new ObjectId($input["id"]))){
    return false;
}

$solution = new TestSolution($db, new ObjectId($input["id"]));
$exams = (array) $user->activeTests ?? [];

if (!in_array($solution->collection, $exams)){
    return false;
}



$response["msg"] = $dictionary->success;
$response["result"] = $solution->getDataForTeacher();
$response["code"] = "Success";

unset($id, $test);
