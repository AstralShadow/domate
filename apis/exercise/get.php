<?php

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\Exercise as Exercise;

$exercise = new Exercise($db, new ObjectId($_id));

if (false === strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "data" => $exercise->dump(),
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

$whitebell->addEventListener("modified_exercise_" . $user->id, function (string $id) use ($exercise, $_id, $whitebell){
    if ($id == $_id && isset($exercise)){
        $data = [
            "id" => $exercise->getId(),
            "data" => $exercise->dump()
        ];
        echo "event: modified_exercise\n";
        echo 'data: ' . json_encode($data);
        echo "\n\n";
    }
    if (connection_aborted()){
        $whitebell->stop();
    }
});

$whitebell->addEventListener("deleted_exercise_" . $user->id, function (string $id) use ($exercise, $_id, $whitebell){
    if ($id == $_id){
        $exercise = null;
        if (!defined("DONT_RUN_WHITEBELL")){
            $whitebell->stop();
        }
    }
    if (connection_aborted()){
        $whitebell->stop();
    }
});

if (!defined("DONT_RUN_WHITEBELL")){
    $whitebell->run();
}
