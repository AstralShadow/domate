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

/*
 * Action
 */

$test = new Test($db, new ObjectId($input["id"]));

if (isset($input["name"]) && is_string($input["name"])){
    $test->name = $input["name"];
}

if (isset($input["description"]) && is_string($input["description"])){
    $test->description = $input["description"];
}

if (isset($input["addContents"]) && is_array($input["addContents"])){
    $accessibleGroups = (array) $user->exerciseGroups ?? [];
    foreach ($input["addContents"] as $groupId => $count){
        if (!is_string($groupId) || !is_int($count) || $count < 1){
            continue;
        }
        if (in_array($groupId, $accessibleGroups)){
            $group = new ExerciseGroup($db, new ObjectId($groupId));
            $test->addExerciseGroup($group, (int) $count);
        }
    }
}

if (isset($input["removeContents"]) && is_array($input["removeContents"])){
    $containedGroupIds = $test->getContentTokens();
    foreach ($containedGroupIds as $token){
        if (in_array($token, $input["removeContents"])){
            $test->removeExerciseGroup(new ObjectId($token));
        }
    }
}

$response["msg"] = $dictionary->success;
// $response["result"] = $test->dump();
$response["code"] = "Success";

