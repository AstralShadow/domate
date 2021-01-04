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

$tests = $user->tests ?? [];
foreach ($tests as $key => $value){
    $tests[$key] = (string) $value;
}


$response["msg"] = $dictionary->success;
$response["result"] = $tests;
$response["code"] = "Success";
