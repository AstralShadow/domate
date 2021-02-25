<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include "include/testsAndTasks.php";

/*  Input  */

$inputOk = true;
if (!isset($input["id"]) || !is_string($input["id"])){
    $inputOk = false;
}
if (isset($input["note"]) && !is_string($input["note"])){
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
    return;
}
unset($inputOk);

$id = (string) $input["id"];
$note = (string) $input["note"];
$start = (int) $input["start"];
$end = (int) $input["end"];
$worktime = (int) $input["worktime"];

if ($start >= $end || time() >= $end || $worktime < 1){
    $response["msg"] = $dictionary->illegalArguments;
    unset($id, $note, $start, $end, $worktime);
    return;
}

if (!in_array($id, (array) $user->tests ?? [])){
    unset($id, $note, $start, $end, $worktime);
    return false;
}


/*  Action  */
$test = new Test($db, new ObjectId($id));
$test->schedule($user, $start, $end, $worktime, $note);
unset($id, $note, $start, $end, $worktime, $test);


$response["code"] = "Success";
$response["msg"] = $dictionary->success;
$response["input"] = $input;

