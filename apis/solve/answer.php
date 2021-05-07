<?php

include "include/testsAndTasks.php";

$_exam;
$_active_exam;
$_exam_solution;
$_question;
$_input;

if (!isset($_input["answer"]) || !is_string($input["answer"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "missing_fields",
        "required" => ["answer"],
        "message" => $dictionary["missing_argument"]
    ]);
    die;
}

$_question->setAnswer($_input["answer"]);

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "success",
    "message" => $dictionary["success"]
]);

require "include/whiteBell.php";
if (isset($whitebell)){
    $whitebell->dispatchEvent("answered_" . $_active_exam_id, $_question_id);
}

return;
