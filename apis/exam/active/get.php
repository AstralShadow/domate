<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\ActiveTest as ActiveTest;
use MathExam\TestSolution as TestSolution;
use MathExam\ExerciseVariant as ExerciseVariant;

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

require "include/whiteBell.php";

if (!isset($whitebell)){
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable", true, 503);
    die;
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$solutions = [];

$on_answer = function (string $question_id) use ($active_exam, $whitebell, $db){
    if (isset($active_exam)){
        $exercise = new ExerciseVariant($db, $question_id);
        $data = [
            "id" => $question_id,
            "data" => $exercise->getDataForTeacher()
        ];
        echo "event: answered\n";
        echo 'data: ' . json_encode($data);
        echo "\n\n";
    }
    if (connection_aborted()){
        $whitebell->stop();
    }
};

$whitebell->addEventListener("joined_" . $_id2, function (string $id) use ($active_exam, $_id2, $whitebell, $db, $solutions, $on_answer){
    if (isset($active_exam)){
        $solution = new TestSolution($db, $id);
        $solutions[$id] = $solution;
        $whitebell->addEventListener("answered_" . $id, $on_answer);
        $data = [
            "solution_id" => $id,
            "active_exam" => $_id2,
            "data" => $solution->getDataForTeacher()
        ];
        echo "event: joined\n";
        echo 'data: ' . json_encode($data);
        echo "\n\n";
    }
    if (connection_aborted()){
        $whitebell->stop();
    }
});

$whitebell->addEventListener("deleted_active_exam_" . $user->id, function (string $id) use ($active_exam, $_id2, $whitebell){
    if ($id == $_id2){
        $active_exam = null;
        if (!defined("DONT_RUN_WHITEBELL")){
            $whitebell->stop();
        }
    }
    if (connection_aborted()){
        $whitebell->stop();
    }
});

foreach ($active_exam->solutions as $solution_id){
    $solutions[$solution_id] = new TestSolution($db, $solution_id);
    $whitebell->addEventListener("answered_" . $solution_id, $on_answer);
}

if (!defined("DONT_RUN_WHITEBELL")){
    $whitebell->run();
}
