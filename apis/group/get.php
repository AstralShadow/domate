<?php

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\ExerciseGroup as ExerciseGroup;

$group = new ExerciseGroup($db, new ObjectId($_id));

if (false === strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "data" => $group->dump(),
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

$whitebell->addEventListener("modified_group_" . $user->id, function (string $id) use ($group, $_id){
    if ($id == $_id && isset($group)){
        $data = [
            "id" => $group->getId(),
            "data" => $group->dump()
        ];
        echo "event: modified_group\n";
        echo 'data: ' . json_encode($data);
        echo "\n\n";
    }
});

$whitebell->addEventListener("deleted_group_" . $user->id, function (string $id) use ($group, $_id, $whitebell){
    if ($id == $_id){
        $group = null;
        if (!defined("DONT_RUN_WHITEBELL")){
            $whitebell->stop();
        }
    }
});

if (!defined("DONT_RUN_WHITEBELL")){
    $whitebell->run();
}
