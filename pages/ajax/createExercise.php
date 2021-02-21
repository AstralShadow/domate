<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MathExam\Exercise as Exercise;

$exercise = Exercise::create($db, $user);

$response["msg"] = $dictionary->success;
$response["result"] = [
    "id" => (string) $exercise->getId()
];
$response["code"] = "Success";

unset($exercise);
