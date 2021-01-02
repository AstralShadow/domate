<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$response["msg"] = $dictionary->unknownError;
$response["code"] = "Failed";

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

$response["msg"] = $dictionary->logoutMessage;
$response["code"] = "Success";
$response["reload"] = "true";
$user->logout();
