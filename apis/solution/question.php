<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

$_exam;
$_active_exam;
$_exam_solution;
$_question;

if (false === strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "data" => $_question->getDataForTeacher(),
        "message" => $dictionary["success"]
    ]);
    return;
}

header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented", true, 503);
echo json_encode(["note" => "use tracking of ActiveTest instead."]);
die;

require "include/whiteBell.php";

if (!isset($whitebell)){
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable", true, 503);
    die;
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$whitebell->addEventListener("answered_" . $user->id, function (string $id) use ($_question, $_question_id, $whitebell){
    if ($id == $_question_id){
        $data = [
            "id" => $_question->getId(),
            "data" => $_question->getDataForTeacher()
        ];
        echo "event: answered\n";
        echo 'data: ' . json_encode($data);
        echo "\n\n";
    }
    if (connection_aborted()){
        $whitebell->stop();
    }
});

if (!defined("DONT_RUN_WHITEBELL")){
    $whitebell->run();
}
