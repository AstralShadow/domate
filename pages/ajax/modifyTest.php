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

$test = new Test($db, new ObjectId($input["id"]));
if (!$test || $test->owner !== $user->user){
    return false;
}

/*
 * Action
 */

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
    unset($accessibleGroups, $groupId, $count, $group);
}

if (isset($input["removeContents"]) && is_array($input["removeContents"])){
    $containedGroupTokens = $test->getContentTokens();
    foreach ($containedGroupTokens as $token){
        if (in_array($token, $input["removeContents"])){
            $test->removeExerciseGroup(new ObjectId($token));
        }
    }
    unset($containedGroupTokens, $token);
}

if (isset($input["move"], $input["position"])){
    if (is_string($input["move"]) && is_int($input["position"])){
        $token = $input["move"];
        $position = $input["position"];
        $containedGroupTokens = $test->getContentTokens();
        if (in_array($token, $containedGroupTokens)){
            $test->moveExrciseGroup(new ObjectId($token), (int) $position);
        }
    }
    unset($containedGroupTokens, $token, $position);
}

$response["msg"] = $dictionary->success;
// $response["result"] = $test->dump();
$response["code"] = "Success";

