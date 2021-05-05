<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\TestSolution as TestSolution;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}

if (!in_array($input["id"], (array) $session->activeTests ?? [])){
    return false;
}


/*
 * Action
 */

$id = new ObjectId($input["id"]);
$testSolution = new TestSolution($db, $id);

$response["code"] = "Success";
$response["result"] = $testSolution->dump();
$response["msg"] = $dictionary->success;

unset($id, $testSolution);

