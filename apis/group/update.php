<?php

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\ExerciseGroup as ExerciseGroup;
use MathExam\Exercise as Exercise;

$group = new ExerciseGroup($db, new ObjectId($_id));
if (!$group){
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
    die;
}
if ($group->owner !== $user->user){
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
    die;
}

if (isset($_input["name"]) && is_string($_input["name"])){
    $group->name = $_input["name"];
}

if (isset($_input["description"]) && is_string($_input["description"])){
    $group->description = $_input["description"];
}

if (isset($_input["add_contents"]) && is_array($_input["add_contents"])){
    $accessible_exercises = (array) $user->exercises ?? [];
    foreach ($_input["add_contents"] as $exercise_id){
        if (!is_string($exercise_id)){
            continue;
        }
        if (in_array($exercise_id, $accessible_exercises)){
            $exercise = new Exercise($db, new ObjectId($exercise_id));
            $group->addExercise($exercise);
        }
    }
    unset($accessible_exercises, $exercise_id, $exercise);
}

if (isset($_input["remove_contents"]) && is_array($_input["remove_contents"])){
    $contained_exercise_ids = $group->getContents();
    foreach ($contained_exercise_ids as $oid){
        if (in_array($oid, $_input["remove_contents"])){
            $exercise = new Exercise($db, new ObjectId($oid));
            $group->removeExercise($exercise);
        }
    }
    unset($contained_exercise_ids, $oid, $exercise);
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "success",
    "id" => $_id,
    "message" => $dictionary["success"]
]);

require "include/whiteBell.php";
if (isset($whitebell)){
    $whitebell->dispatchEvent("modified_group_" . $user->id, $_id);
}

return;

