<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include "include/testsAndTasks.php";

use \MongoDB\BSON\ObjectId as ObjectId;
use MathExam\Test as Test;
use MathExam\ActiveTest as ActiveTest;
use MathExam\TestSolution as TestSolution;

/* Validation */
if (!isset($input["test"]) || !is_string($input["test"])){
    $response["code"] = "IllegalInput";
    $response["msg"] = $dictionary->joinTest["unknownTest"];
    return;
}

$activeTests = $session->activeTests;
if (isset($activeTests[$input["test"]])){
    $response["code"] = "Success";
    $response["msg"] = $dictionary->joinTest["alreadyJoined"];
    $response["result"] = (string) $activeTests[$input["tests"]];
    return;
}

$id = ActiveTest::getIdFromKey($db, trim($input["test"]));
if (!isset($id)){
    $response["code"] = "NotExisting";
    $response["msg"] = $dictionary->joinTest["notExisting"];
    unset($id);
    return;
}
$activeTest = new ActiveTest($db, new ObjectId($id));
$start = $activeTest->start->toDateTime()->getTimestamp();
$end = $activeTest->end->toDateTime()->getTimestamp();

if ($start < time()){
    $response["code"] = "TooEarly";
    $response["msg"] = $dictionary->joinTest["notStartedYet"];
    return;
}
if ($end > time()){
    $response["code"] = "TooLate";
    $response["msg"] = $dictionary->joinTest["alreadyExpired"];
    return;
}

if (!Test::exists($db, new ObjectId($activeTest->test))){
    $response["code"] = "NotExisting";
    $response["msg"] = $dictionary->joinTest["sourceDeleted"];
    return;
}

if (!isset($input["identification"]) || !is_string($input["identification"])){
    $response["code"] = "IllegalInput";
    $response["msg"] = $dictionary->missingArgument;
    return;
}


/* Action */
$testSolution = TestSolution::create($db, $activeTest);
$testSolution->identification = trim($input["identification"]);

$addToActiveQuery = ['$set' => ["activeTests." . trim($input["test"]) => $testSolution->getId()]];
$session->update($addToActiveQuery);

$response["code"] = "Success";
$response["msg"] = $dictionary->joinTest["youJoined"];
$response["result"] = (string) $testSolution->getId();
