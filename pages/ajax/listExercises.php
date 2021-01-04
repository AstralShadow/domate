<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Validation
 */

if (!isset($user)){
    $response["msg"] = $dictionary->formMessages["youAreNotLoggedIn"];
    $response["code"] = "Forbidden";
    $response["reload"] = true;
    unset($data);
    return;
}

/*
 * Action
 */

$exercises = $user->exercises ?? [];
foreach ($exercises as $key => $value){
    $exercises[$key] = (string) $value;
}


$response["msg"] = $dictionary->success;
$response["result"] = $exercises;
$response["code"] = "Success";
