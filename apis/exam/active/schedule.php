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

$inputOk = true;
if (isset($_input["note"]) && !is_string($_input["note"])){
    $inputOk = false;
}
if (isset($_input["question"]) && !is_string($_input["question"])){
    $inputOk = false;
}
if (!isset($_input["start"]) || !is_numeric($_input["start"])){
    $inputOk = false;
}
if (!isset($_input["end"]) || !is_numeric($_input["end"])){
    $inputOk = false;
}
if (!isset($_input["worktime"]) || !is_numeric($_input["worktime"])){
    $inputOk = false;
}
if (!$inputOk){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "400",
        "message" => $dictionary["illegal_arguments"]
    ]);
    die;
}

$note = isset($_input["note"]) ? (string) $_input["note"] : null;
$start = (int) $_input["start"];
$end = (int) $_input["end"];
$worktime = (int) $_input["worktime"];
$question = isset($_input["question"]) ? (string) $_input["question"] : null;

if ($start >= $end || time() >= $end || $worktime < 1){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "400",
        "message" => $dictionary["illegal_arguments"]
    ]);
    die;
}

/*  Action  */
$exam = new Test($db, new ObjectId($_id));
$active_exam = $test->schedule($user, $start, $end, $worktime, $question, $note);

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "200",
    "key" => $activeTest->getKey(),
    "id" => $activeTest->getId()
]);

require "include/whiteBell.php";

if (isset($whitebell)){
    $whitebell->dispatchEvent("new_active_exam_" . $user->id, $activeTest->getId());
}

return;
