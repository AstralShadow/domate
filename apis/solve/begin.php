<?php

require "include/testsAndTasks.php";

use \MongoDB\BSON\ObjectId as ObjectId;
use MathExam\Test as Test;
use MathExam\ActiveTest as ActiveTest;
use MathExam\TestSolution as TestSolution;

$_input;
$_exam;
$_active_exam;

$start = $_active_exam->start->toDateTime()->getTimestamp();
$end = $_active_exam->end->toDateTime()->getTimestamp();

if ($start > time()){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "too_early",
        "message" => $dictionary["not_started_yet"]
    ]);
    die;
}
if ($end < time()){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "too_late",
        "message" => $dictionary["already_expired"]
    ]);
    die;
}

if (!isset($_input["identification"]) || !is_string($_input["identification"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "missing_fields",
        "required" => ["identification"],
        "message" => $dictionary["missing_argument"]
    ]);
    die;
}

$_exam_solution = TestSolution::create($db, $activeTest);
$_exam_solution_id = $_exam_solution->getId();
$add_to_active_query = [
    '$set' => [
        "activeTests." . $_key => $_exam_solution_id
    ]
];
$session->update($add_to_active_query);

require "include/whiteBell.php";
if (isset($whitebell)){
    $whitebell->dispatchEvent("joined_" . $_active_exam_id, $_exam_solution_id);
}

require "apis/solve/get.php";

return;
