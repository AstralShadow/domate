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
$exercises = (array) $user->exercises ?? [];

if (!in_array($input["id"], $exercises)){
    return false;
}
$id = new ObjectId($input["id"]);

/*
 * Action
 */
$removeUserAccess = [
    '$pull' => [
        "exercises" => $id
    ]
];
$user->update($removeUserAccess);

$response["msg"] = $dictionary->success;
$response["code"] = "Success";
