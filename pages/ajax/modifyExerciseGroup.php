<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\ExerciseGroup as ExerciseGroup;
use MathExam\Exercise as Exercise;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
$groups = (array) $user->exerciseGroups ?? [];

if (!in_array($input["id"], $groups)){
    return false;
}

$group = new ExerciseGroup($db, new ObjectId($input["id"]));
if (!$group || $group->owner !== $group->user){
    return false;
}

/*
 * Action
 */

if (isset($input["name"]) && is_string($input["name"])){
    $group->name = $input["name"];
}

if (isset($input["description"]) && is_string($input["description"])){
    $group->description = $input["description"];
}

if (isset($input["addContents"]) && is_array($input["addContents"])){
    $accessibleExercises = (array) $user->exercises ?? [];
    foreach ($input["addContents"] as $exerciseId => $count){
        if (!is_string($exerciseId) || !is_int($count) || $count < 1){
            continue;
        }
        if (in_array($exerciseId, $accessibleExercises)){
            $exercise = new Exercise($db, new ObjectId($exerciseId));
            $group->addExercise($exercise, (int) $count);
        }
    }
}

if (isset($input["removeContents"]) && is_array($input["removeContents"])){
    $containedExerciseIds = $test->getContentTokens();
    foreach ($containedExerciseIds as $token){
        if (in_array($token, $input["removeContents"])){
            $group->removeExercise(new ObjectId($token));
        }
    }
}

$response["msg"] = $dictionary->success;
// $response["result"] = $group->dump();
$response["code"] = "Success";

