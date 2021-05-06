<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\TestSolution as TestSolution;
use MathExam\ExerciseVariant as ExerciseVariant;

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
if (!ExerciseVariant::exists($db, new ObjectId($input["id"]))){
    return false;
}
$variant = new ExerciseVariant($db, new ObjectId($input["id"]));
if (!TestSolution::exists($db, new ObjectId($variant->paper))){
    return false;
}

$solution = new TestSolution($db, new ObjectId($variant->paper));
$exams = (array) $user->activeTests ?? [];

if (!in_array($solution->collection, $exams)){
    echo 3;
    return false;
}

if (!isset($input["true"]) || !is_bool($input["true"])){
    return false;
}

$variant->submitCheck((bool) $input["true"]);

$response["msg"] = $dictionary->success;
$response["code"] = "Success";

unset($id, $test);
