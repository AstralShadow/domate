<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;

/*
 * Validate
 */

if (!isset($input) || !is_array($input) || !isset($input["id"])){
    return false;
}
$tests = (array) $user->tests ?? [];

if (!in_array($input["id"], $tests)){
    unset($tests);
    return false;
}
unset($tests);

$id = new ObjectId($input["id"]);

/*
 * Action
 */
$removeUserAccess = [
    '$pull' => [
        "tests" => $id
    ]
];
$user->update($removeUserAccess);
unset($removeUserAccess);

$response["msg"] = $dictionary->success;
$response["code"] = "Success";
unset($id);
