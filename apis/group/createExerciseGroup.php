<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MathExam\ExerciseGroup as ExerciseGroup;

$group = ExerciseGroup::create($db, $user);

$response["msg"] = $dictionary->success;
$response["result"] = [
    "id" => (string) $group->getId()
];
$response["code"] = "Success";

unset($group);
