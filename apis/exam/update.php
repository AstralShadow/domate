<?php

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\Test as Test;
use MathExam\ExerciseGroup as ExerciseGroup;

$test = new Test($db, new ObjectId($_id));
if (!$test){
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
    die;
}
if ($test->owner !== $user->user){
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
    die;
}

if (isset($_input["name"]) && is_string($_input["name"])){
    $test->name = $_input["name"];
}

if (isset($_input["description"]) && is_string($_input["description"])){
    $test->description = $_input["description"];
}

if (isset($_input["add_contents"]) && is_array($_input["add_contents"])){
    $accessible_groups = (array) $user->exerciseGroups ?? [];
    foreach ($_input["add_contents"] as $group_id => $count){
        if (!is_string($group_id) || !is_int($count) || $count < 1){
            continue;
        }
        if (in_array($group_id, $accessible_groups)){
            $group = new ExerciseGroup($db, new ObjectId($group_id));
            $test->addExerciseGroup($group, (int) $count);
        }
    }
    unset($accessible_groups, $group_id, $count, $group);
}

if (isset($_input["remove_contents"]) && is_array($_input["remove_contents"])){
    $contained_group_tokens = $test->getContentTokens();
    foreach ($contained_group_tokens as $token){
        if (in_array($token, $_input["remove_contents"])){
            $test->removeExerciseGroup(new ObjectId($token));
        }
    }
    unset($contained_group_tokens, $token);
}

if (isset($_input["move"], $_input["position"])){
    if (is_string($_input["move"]) && is_int($_input["position"])){
        $token = $_input["move"];
        $position = $_input["position"];
        $contained_group_tokens = $test->getContentTokens();
        if (in_array($token, $contained_group_tokens)){
            $test->moveExrciseGroup(new ObjectId($token), (int) $position);
        }
        unset($contained_group_tokens, $token, $position);
    }
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "success",
    "id" => (string) $_id,
    "message" => $dictionary["success"]
]);

require "include/whiteBell.php";
if (isset($whitebell)){
    $whitebell->dispatchEvent("modified_exam_" . $user->id, $_id);
}

return;
