<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;
use MathExam\Test as Test;
use MathExam\ExerciseGroup as ExerciseGroup;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
$tests = (array) $user->tests ?? [];

if (!in_array($input["id"], $tests)){
    return false;
}
$id = new ObjectId($input["id"]);

/*
 * Action
 */

$test = new Test($db, $id);
if (isset($input["name"]) && is_string($input["name"])){
    $test->name = $input["name"];
}

if (isset($input["description"]) && is_string($input["description"])){
    $test->description = $input["description"];
}

if (isset($input["addExerciseGroups"]) && is_array($input["addExerciseGroups"])){
    $accessibleGroups = (array) $user->exerciseGroups ?? [];
    foreach ($input["addExerciseGroups"] as $groupId => $count){
        if (!is_string($groupId) || !is_int($count) || $count < 1){
            continue;
        }
        if (in_array($groupId, $accessibleGroups)){
            $group = new ExerciseGroup($db, ObjectId($groupId));
            $test->addExerciseGroup($group, (int) $count);
        }
    }
}

if (isset($input["removeExerciseGroups"]) && is_array($input["removeExerciseGroups"])){
    $containedGroupIds = $test->getExerciseGroupIds();
    foreach ($containedGroupIds as $groupId){
        if (in_array($groupId, $input["removeExerciseGroups"])){
            $group = new ExerciseGroup($db, new ObjectId($groupId));
            $test->removeExerciseGroup($group);
        }
    }
}

$response["msg"] = $dictionary->success;
$response["result"] = $test->dump();
$response["code"] = "Success";

