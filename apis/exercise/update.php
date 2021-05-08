<?php

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\Exercise as Exercise;

$exercise = new Exercise($db, new ObjectId($_id));
if (!$exercise){
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
    die;
}
if ($exercise->owner !== $user->user){
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
    die;
}

if (isset($_input["name"]) && is_string($_input["name"])){
    $exercise->name = (string) $_input["name"];
}

if (isset($_input["description"]) && is_string($_input["description"])){
    $exercise->description = (string) $_input["description"];
}

if (isset($_input["question"]) && is_string($_input["question"])){
    $exercise->question = (string) $_input["question"];
}

if (isset($_input["answer"]) && is_string($_input["answer"])){
    $exercise->answer = (string) $_input["answer"];
}

if (isset($_input["useAnswer"]) && is_bool($_input["useAnswer"])){
    $exercise->useAnswer = (bool) $_input["useAnswer"];
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "success",
    "id" => (string) $_id,
    "message" => $dictionary["success"]
]);

require "include/whiteBell.php";
if (isset($whitebell)){
    $whitebell->dispatchEvent("modified_exercise_" . $user->id, $_id);
}

return;

