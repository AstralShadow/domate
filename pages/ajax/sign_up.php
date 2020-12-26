<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/functions/generateSecureToken.php";
require "include/functions/verifySecureToken.php";

$response["msg"] = $dictionary->unknown_error;
$response["code"] = "failed";

/*
 * Prevalidation
 */

if (isset($session->lastSentFormTime)){
    $lastTime = $session->lastSentFormTime->toDateTime()->format("U");
    if (time() - $lastTime < 2){
        $response["msg"] = $dictionary->form_messages["too_fast"];
        $response["code"] = "too_fast";
        unset($lastTime);
        return;
    }
    unset($lastTime);
}

$session->lastSentFormTime = new \MongoDB\BSON\UTCDateTime();

/*
 * Input
 */

$data = [];

foreach (["user", "pwd", "pwd2", "token"] as $key){
    if (isset($_POST[$key]) && is_string($_POST[$key])){
        $data[$key] = trim($_POST[$key]);
    }else{
        $response["msg"] = $dictionary->form_messages["missing_fields"];
        $response["code"] = "missing_fields";
        unset($data);
        return;
    }
}

/*
 * Validation
 */

if (isset($user)){
    $response["msg"] = $dictionary->form_messages["you_are_already_logged_in"];
    $response["code"] = "forbidden";
    $response["reload"] = true;
    unset($data);
    return;
}

$ok = verifySecureToken($session, "sign_up", $data["token"]);
$response["newToken"] = generateSecureToken($session, "sign_up");
if (!$ok){
    $response["msg"] = $dictionary->form_messages["invalid_token"];
    $response["code"] = "invalid_token";
    unset($data, $ok);
    return;
}
unset($data["token"], $ok);

if ($data["pwd"] !== $data["pwd2"]){
    $response["msg"] = $dictionary->sign_up_messages["different_passwords"];
    $response["code"] = "different_passwords";
    unset($data);
    return;
}
unset($data["pwd2"]);

if (file_exists("data/commonPasswords.json")){
    $commonPasswords = json_decode(file_get_contents("data/commonPasswords.json"), true);
    if (in_array($data["pwd"], $commonPasswords)){
        $response["msg"] = $dictionary->sign_up_messages["password_already_used"];
        return;
    }
    unset($commonPasswords);
}

/*
 * Action
 */

$errorByte = 0;
$userData = [
    "user" => $data["user"],
    "pwd" => $data["pwd"]
];
unset($data);

\Main\User::create($db, $userData, $errorByte);
unset($userData);

if ($errorByte !== 0){
    $response["msg"] = \Main\User::getErrorMessage($dictionary, $errorByte);
    $response["code"] = "user_error_" . $errorByte;
    unset($errorByte);
    return;
}
unset($errorByte);

$response["msg"] = $dictionary->sign_up_messages["success"];
$response["code"] = "success";
