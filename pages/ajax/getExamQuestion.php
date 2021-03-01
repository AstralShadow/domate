<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\TestVariant as TestVariant;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}



/*
 * Action
 */

$id = new ObjectId($input["id"]);
if (!TestVariant::exists($db, $id)){
    return false;
}
$variant = new TestVariant($db, $id);

if (!in_array($input["id"], (array) $session->activeTests ?? [])){
    return false;
}

$response["code"] = "Success";
$response["result"] = $testSolution->dump();
$response["msg"] = $dictionary->success;

unset($id, $testSolution);

