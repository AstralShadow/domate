<?php

require "include/whiteBell.php";

if (!isset($whitebell)){
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable", true, 503);
    die;
}

require "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId as ObjectId;
use MathExam\Test as Test;
use MathExam\ExerciseGroup as ExerciseGroup;
use MathExam\Exercise as Exercise;

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$whitebell->addEventListener("new_exam_" . $user->id, function (string $id) use ($whitebell){
    echo "event: new_exam\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("deleted_exam_" . $user->id, function (string $id) use ($whitebell){
    echo "event: deleted_exam\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("modified_exam_" . $user->id, function (string $exam_id) use ($db, $whitebell){
    $exam = new Test($db, new ObjectId($exam_id));
    $data = [
        "id" => $exam_id,
        "data" => $exam->dump()
    ];
    echo "event: modified_exam\n";
    echo 'data: ' . json_encode($data);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("new_active_exam_" . $user->id, function (string $id) use ($whitebell){
    echo "event: new_active_exam\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("deleted_active_exam_" . $user->id, function (string $id) use ($whitebell){
    echo "event: deleted_active_exam\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("new_group_" . $user->id, function (string $id) use ($whitebell){
    echo "event: new_group\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("deleted_group_" . $user->id, function (string $id) use ($whitebell){
    echo "event: deleted_group\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("modified_group_" . $user->id, function (string $id) use ($db, $whitebell){
    $group = new ExerciseGroup($db, new ObjectId($id));
    $data = [
        "id" => $id,
        "data" => $group->dump()
    ];
    echo "event: modified_group\n";
    echo 'data: ' . json_encode($data);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("new_exercise_" . $user->id, function (string $id) use ($whitebell){
    echo "event: new_exercise\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("deleted_exercise_" . $user->id, function (string $id) use ($whitebell){
    echo "event: deleted_exercise\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

$whitebell->addEventListener("modified_exercise_" . $user->id, function (string $id) use ($db, $whitebell){
    $exercise = new Exercise($db, new ObjectId($id));
    $data = [
        "id" => $id,
        "data" => $exercise->dump()
    ];
    echo "event: modified_exercise\n";
    echo 'data: ' . json_encode($data);
    echo "\n\n";
    ob_end_flush();
    flush();
    if (connection_aborted()){
        $whitebell->stop();
    }
    set_time_limit(60);
});

if (!defined("DONT_RUN_WHITEBELL")){
    flush();
    set_time_limit(60);
    $whitebell->run();
}


