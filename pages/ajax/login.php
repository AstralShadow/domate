<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/secureTokens.php";

use MongoDB\BSON\UTCDateTime as UTCDateTime;
use Identification\User as User;

$response["msg"] = $dictionary->unknownError;
$response["code"] = "Failed";

/*
 * Prevalidation
 */

if (isset($session->lastSentFormTime)){
    $lastTime = $session->lastSentFormTime->toDateTime()->format("U");
    if (time() - $lastTime < 2){
        $response["msg"] = $dictionary->formMessages["tooFast"];
        $response["code"] = "TooFast";
        unset($lastTime);
        return;
    }
    unset($lastTime);
}

$session->lastSentFormTime = new UTCDateTime();

/*
 * Input
 */

$data = [];

foreach (["user", "pwd", "token"] as $key){
    if (isset($_POST[$key]) && is_string($_POST[$key])){
        $data[$key] = trim($_POST[$key]);
    } else{
        $response["msg"] = $dictionary->formMessages["missingFields"];
        $response["code"] = "MissingFields";
        unset($data);
        return;
    }
}


/*
 * Validation
 */

if (isset($user)){
    $response["msg"] = $dictionary->formMessages["youAreAlreadyLoggedIn"];
    $response["code"] = "Forbidden";
    $response["reload"] = true;
    unset($data);
    return;
}

$ok = verifySecureToken($session, "login", $data["token"]);
$response["newToken"] = generateSecureToken($session, "login");
if (!$ok){
    $response["msg"] = $dictionary->formMessages["invalidToken"];
    $response["code"] = "InvalidToken";
    unset($data, $ok);
    return;
}
unset($data["token"], $ok);


/*
 * Action
 */

$errorByte = 0;
$userData = [
    "user" => $data["user"],
    "pwd" => $data["pwd"]
];
unset($data);

$user = User::authorize($db, $session, $userData, $errorByte);
unset($userData);

if ($errorByte !== 0){
    $response["msg"] = User::getErrorMessage($dictionary, $errorByte);
    $response["code"] = "UserError: " . $errorByte;
    unset($errorByte);
    return;
}
unset($errorByte);

$response["msg"] = $dictionary->loginMessages["success"];
$response["code"] = "Success";
$response["reload"] = true;
