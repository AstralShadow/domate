<?php

include "include/testsAndTasks.php";

use MathExam\Exercise as Exercise;

$exercise = Exercise::create($db, $user);
$_id = (string) $exercise->getId();

if (!defined("DONT_RETURN_CREATED_ID")){
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "id" => $_id,
        "message" => $dictionary["success"]
    ]);
}

require "include/whiteBell.php";
if (isset($whitebell)){
    $whitebell->dispatchEvent("new_exercise_" . $user->id, $_id);
}

return;
