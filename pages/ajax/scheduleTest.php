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

/*  Input  */

$inputOk = true;
if (!isset($input["id"]) || !is_string($input["id"])){
    $inputOk = false;
}
if (isset($input["note"]) && !is_string($input["note"])){
    $inputOk = false;
}
if (isset($input["question"]) && !is_string($input["question"])){
    $inputOk = false;
}
if (!isset($input["start"]) || !is_numeric($input["start"])){
    $inputOk = false;
}
if (!isset($input["end"]) || !is_numeric($input["end"])){
    $inputOk = false;
}
if (!isset($input["worktime"]) || !is_numeric($input["worktime"])){
    $inputOk = false;
}
if (!$inputOk){
    unset($inputOk);
    $response["msg"] = $dictionary->illegalArguments;
    $response["code"] = "Illegal input";
    return;
}
unset($inputOk);

$id = (string) $input["id"];
$note = isset($input["note"]) ? (string) $input["note"] : null;
$start = (int) $input["start"];
$end = (int) $input["end"];
$worktime = (int) $input["worktime"];
$question = isset($input["idQuestion"]) ? (string) $input["question"] : null;

if ($start >= $end || time() >= $end || $worktime < 1){
    $response["msg"] = $dictionary->illegalArguments;
    $response["code"] = "OutdatedInput";
    unset($id, $note, $start, $end, $worktime);
    return;
}

if (!in_array($id, (array) $user->tests ?? [])){
    unset($id, $note, $start, $end, $worktime);
    return false;
}


/*  Action  */
$test = new Test($db, new ObjectId($id));
$activeTest = $test->schedule($user, $start, $end, $worktime, $question, $note);
$template = $dictionary->successfulTestSchedule;
$key = $activeTest->getKey();

unset($id, $note, $start, $end, $worktime, $test, $question, $activeTest);

$response["code"] = "Success";
$response["msg"] = $template;
$response["key"] = $key;
unset($template, $key, $template);
