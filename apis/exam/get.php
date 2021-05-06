<?php

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\Test as Test;

$test = new Test($db, new ObjectId($_id));

if (false === strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "data" => $test->dump(),
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

$whitebell->addEventListener("modified_exam_" . $user->id, function (string $id) use ($test, $_id){
    if ($id == $_id && isset($test)){
        $data = [
            "id" => $test->getId(),
            "data" => $test->dump()
        ];
        echo "event: modified_exam\n";
        echo 'data: ' . json_encode($data);
        echo "\n\n";
    }
});

$whitebell->addEventListener("deleted_exam_" . $user->id, function (string $id) use ($test, $_id, $whitebell){
    if ($id == $_id){
        $test = null;
        if (!defined("DONT_RUN_WHITEBELL")){
            $whitebell->stop();
        }
    }
});

if (!defined("DONT_RUN_WHITEBELL")){
    $whitebell->run();
}
