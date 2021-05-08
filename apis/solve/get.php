<?php

include "include/testsAndTasks.php";

$_active_exam;
$_exam;
$_exam_solution;

$answer = [
    "joined" => true,
    "solution" => $_exam_solution->dump(),
    "identification" => $_active_exam->question,
    "start" => $_active_exam->start->toDateTime()->getTimestamp(),
    "end" => $_active_exam->end->toDateTime()->getTimestamp(),
    "worktime" => (int) $_active_exam->worktime,
    "question_count" => count($_exam->contents)
];

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "success",
    "data" => $answer,
    "message" => $dictionary["success"]
]);
return;
