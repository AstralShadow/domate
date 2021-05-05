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
    unset($groups);
    return false;
}
unset($groups);

$group = new ExerciseGroup($db, new ObjectId($input["id"]));
if (!$group || $group->owner !== $user->user){
    $response["msg"] = "You lack owhership here.";
    return;
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
    foreach ($input["addContents"] as $exerciseId){
        if (!is_string($exerciseId)){
            continue;
        }
        if (in_array($exerciseId, $accessibleExercises)){
            $exercise = new Exercise($db, new ObjectId($exerciseId));
            $group->addExercise($exercise);
        }
    }
    unset($accessibleExercises, $exerciseId, $exercise);
}

if (isset($input["removeContents"]) && is_array($input["removeContents"])){
    $containedExerciseIds = $group->getContents();
    foreach ($containedExerciseIds as $oid){
        if (in_array($oid, $input["removeContents"])){
            $exercise = new Exercise($db, new ObjectId($oid));
            $group->removeExercise($exercise);
        }
    }
    unset($containedExerciseIds, $oid, $exercise);
}

$response["msg"] = $dictionary->success;
$response["code"] = "Success";
unset($group);

