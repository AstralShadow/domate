<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$response["msg"] = $dictionary->unknown_error;
$response["code"] = "failed";

/*
 * Validation
 */

if (!isset($user)){
    $response["msg"] = $dictionary->form_messages["you_are_not_logged_in"];
    $response["code"] = "forbidden";
    $response["reload"] = true;
    unset($data);
    return;
}

/*
 * Action
 */

$response["msg"] = $dictionary->logout_message;
$response["code"] = "success";
$response["reload"] = "true";
$user->logout();
