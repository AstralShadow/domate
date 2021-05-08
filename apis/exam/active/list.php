<?php

use MongoDB\BSON\ObjectId;
use MathExam\Test as Test;

if (false === strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    $active_exams = (new Test($db, new ObjectId($_id)))->listExams($user);

    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "data" => $active_exams,
        "message" => $dictionary["success"]
    ]);

    return;
}

require "include/whiteBell.php";

if (!isset($whitebell)){
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable", true, 503);
    die;
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$whitebell->addEventListener("new_active_exam_" . $user->id, function (string $id){
    echo "event: new_active_exam\n";
    echo 'data: {"id": "' . $id . '"}';
    echo "\n\n";
});

$whitebell->addEventListener("deleted_active_exam_" . $user->id, function (string $id){
    echo "event: deleted_active_exam\n";
    echo 'data: {"id": "' . $id . '"}';
    echo "\n\n";
});

if (!defined("DONT_RUN_WHITEBELL")){
    $whitebell->run();
}
