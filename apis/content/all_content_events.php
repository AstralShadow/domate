<?php

require "include/whiteBell.php";
require "include/testsAndTasks.php";

if (!isset($whitebell)){
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable", true, 503);
    die;
}

use MongoDB\BSON\ObjectId as ObjectId;
use MathExam\Test as Test;
use MathExam\ExerciseGroup as ExerciseGroup;
use MathExam\Exercise as Exercise;
use MathExam\TestSolution as TestSolution;
use MathExam\ActiveTest as ActiveTest;
use MathExam\ExerciseVariant as ExerciseVariant;

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$whitebell->addEventListener("new_exam_" . $user->id, function (string $id){
    echo "event: new_exam\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
});

$whitebell->addEventListener("deleted_exam_" . $user->id, function (string $id){
    echo "event: deleted_exam\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
});

$whitebell->addEventListener("modified_exam_" . $user->id, function (string $exam_id) use ($db){
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
});

$on_solution_mark = function (string $question_id)
use ($db){
    $exercise = new ExerciseVariant($db, new ObjectId($question_id));
    $data = [
        "id" => $question_id,
        "solution" => (string) $exercise->paper,
        "data" => $exercise->getDataForTeacher()
    ];
    echo "event: marked\n";
    echo 'data: ' . json_encode($data);
    echo "\n\n";
    ob_end_flush();
    flush();
};
$on_solution_answer = function (string $question_id)
use ($db){
    $exercise = new ExerciseVariant($db, new ObjectId($question_id));
    $data = [
        "id" => $question_id,
        "solution" => (string) $exercise->paper,
        "data" => $exercise->getDataForTeacher()
    ];
    echo "event: answered\n";
    echo 'data: ' . json_encode($data);
    echo "\n\n";
    ob_end_flush();
    flush();
};

$on_active_exam_joined = function (string $id)
use ($whitebell, $db, $on_solution_answer, $on_solution_mark){
    $solution = new TestSolution($db, new ObjectId($id));
    $data = [
        "exam" => (string) $solution->origin,
        "active_exam" => (string) $solution->collection,
        "solution" => $id,
        "data" => $solution->getDataForTeacher()
    ];

    if ($solution->finished->toDateTime()->getTimestamp() > time()){
        $whitebell->addEventListener("answered_" . $id, $on_solution_answer);
    }
    $whitebell->dispatchEvent("marked_" . $id, $on_solution_mark);

    echo "event: joined\n";
    echo 'data: ' . json_encode($data);
    echo "\n\n";
    ob_end_flush();
    flush();
};

foreach ($user->activeTests as $id){
    $id = new ObjectId((string) $id);
    if (!ActiveTest::exists($db, $id)){
        continue;
    }
    $active_exam = new ActiveTest($db, $id);
    $end = $active_exam->end->toDateTime()->getTimestamp();
    if ($end > time()){
        $whitebell->addEventListener("joined_" . $id, $on_active_exam_joined);
    }
    foreach ($active_exam->solutions as $sln_id){
        if (!TestSolution::exists($db, $sln_id)){
            continue;
        }
        $solution = new TestSolution($db, $sln_id);
        $finished = $solution->finished->toDateTime()->getTimestamp();
        if ($finished > time()){
            $whitebell->addEventListener("answered_" . $sln_id, $on_solution_answer);
        }
        $whitebell->addEventListener("marked_" . $sln_id, $on_solution_mark);
    }
}
unset($id, $active_exam, $end, $sln_id, $solution, $finished);

$whitebell->addEventListener("new_active_exam_" . $user->id,
                             function (string $id)
    use ($whitebell, $on_active_exam_joined){
        echo "event: new_active_exam\n";
        echo "data: " . json_encode(["id" => $id]);
        echo "\n\n";
        ob_end_flush();
        flush();

        $whitebell->addEventListener("joined_" . $id, $on_active_exam_joined);
    });

$whitebell->addEventListener("deleted_active_exam_" . $user->id,
                             function (string $id)
    use ($whitebell, $on_active_exam_joined){
        echo "event: deleted_active_exam\n";
        echo "data: " . json_encode(["id" => $id]);
        echo "\n\n";
        ob_end_flush();
        flush();
        $whitebell->removeEventListener("joined_" . $id, $on_active_exam_joined);
    });

$whitebell->addEventListener("new_group_" . $user->id, function (string $id){
    echo "event: new_group\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
});

$whitebell->addEventListener("deleted_group_" . $user->id, function (string $id){
    echo "event: deleted_group\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
});

$whitebell->addEventListener("modified_group_" . $user->id, function (string $id) use ($db){
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
});

$whitebell->addEventListener("new_exercise_" . $user->id, function (string $id){
    echo "event: new_exercise\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
});

$whitebell->addEventListener("deleted_exercise_" . $user->id, function (string $id){
    echo "event: deleted_exercise\n";
    echo "data: " . json_encode(["id" => $id]);
    echo "\n\n";
    ob_end_flush();
    flush();
});

$whitebell->addEventListener("modified_exercise_" . $user->id, function (string $id) use ($db){
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
});

if (!defined("DONT_RUN_WHITEBELL")){
    echo "event: connected\n";
    echo "\n\n";
    ob_end_flush();
    flush();
    $whitebell->run();
}


