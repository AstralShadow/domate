<?php

include "include/testsAndTasks.php";

use MathExam\ExerciseVariant as ExerciseVariant;

$_exam_solution;
$_question;

if (!isset($_input["true"]) || !is_bool($_input["true"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "missing_fields",
        "required" => ["true"],
        "message" => $dictionary["missing_argument"]
    ]);
    return false;
}

$_question->submitCheck((bool) $_input["true"]);

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "success",
    "message" => $dictionary["success"]
]);

require "include/whiteBell.php";
if (isset($whitebell)){
    $whitebell->dispatchEvent("marked_" . $_exam_solution_id, $_question_id);
}

return;

