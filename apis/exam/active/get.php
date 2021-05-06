<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\ActiveTest as ActiveTest;

$active_exam = new ActiveTest($db, new ObjectId($_id2));

if (false === strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "data" => $active_exam->dump(),
        "message" => $dictionary["success"]
    ]);
    return;
}

header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented", true, 501);
die;

// Modifying not implemented after all.

require "include/whiteBell.php";

if (!isset($whitebell)){
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable", true, 503);
    die;
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$whitebell->addEventListener("modified_active_exam_" . $user->id, function (string $id) use ($active_exam, $_id2){
    if ($id == $_id2 && isset($active_exam)){
        $data = [
            "id" => $active_exam->getId(),
            "data" => $active_exam->dump()
        ];
        echo "event: modified_exam\n";
        echo 'data: ' . json_encode($data);
        echo "\n\n";
    }
});

$whitebell->addEventListener("deleted_active_exam_" . $user->id, function (string $id) use ($active_exam, $_id2, $whitebell){
    if ($id == $_id2){
        $active_exam = null;
        if (!defined("DONT_RUN_WHITEBELL")){
            $whitebell->stop();
        }
    }
});

if (!defined("DONT_RUN_WHITEBELL")){
    $whitebell->run();
}
