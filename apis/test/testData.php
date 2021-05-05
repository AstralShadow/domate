<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\Test as Test;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
$tests = (array) $user->tests ?? [];

if (!in_array($input["id"], $tests)){
    unset($tests);
    return false;
}
unset($tests);
$id = new ObjectId($input["id"]);

/*
 * Action
 */

$test = new Test($db, $id);
$response["msg"] = $dictionary->success;
$response["result"] = $test->dump();
$response["code"] = "Success";

unset($id, $test);
