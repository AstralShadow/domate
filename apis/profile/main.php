<?php

use MongoDB\BSON\UTCDateTime as UTCDateTime;

require "include/dictionary.php";
require "include/secureTokens.php";

$isUser = isset($user);
if (!isset($_path[0])){
    $_path[0] = "";
}

/* 404 */
if (!in_array($_path[0], ["sign-up", "login", "logout", "get-token"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
    echo json_encode([
        "code" => "404",
        "message" => $dictionary["404"]
    ]);
    die;
}

/* GET  logout */
if ($_path[0] == "logout"){
    if ($_method != "GET"){
        header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed", true, 405);
        echo json_encode([
            "code" => "405_needs_GET",
            "message" => $dictionary["405_needs_GET"]
        ]);
        die;
    }

    if (!$isUser){
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
        echo json_encode([
            "code" => "you_are_not_authorized",
            "message" => $dictionary["you_are_not_authorized"]
        ]);
        die;
    }

    require "apis/profile/logout.php";
    return;
}

/* GET  get-token */
if ($_path[0] == "get-token"){
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "token",
        "token" => generateSecureToken($session, "profile")
    ]);
    die;
}


/* Token */
if (!isset($_input["token"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "missing_token",
        "message" => $dictionary["missing_token"],
        "hint" => $dictionary["missing_token_hint"]
    ]);
    die;
}
if (!verifySecureToken($session, "profile", $_input["token"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "invalid_token",
        "message" => $dictionary["invalid_token"],
        "hint" => $dictionary["missing_token_hint"]
    ]);
    die;
}

/* Cooldown */
if (isset($session->lastSentFormTime)){
    $lastTime = $session->lastSentFormTime->toDateTime()->format("U");
    if (time() - $lastTime < 2){
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
        echo json_encode([
            "code" => "too_fast",
            "message" => $dictionary["too_fast"]
        ]);
        die;
    }
}
$session->lastSentFormTime = new UTCDateTime();

/* POST  sign up */
if ($_path[0] == "sign-up"){
    if ($_method != "POST"){
        header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed", true, 405);
        echo json_encode([
            "code" => "405_needs_POST",
            "message" => $dictionary["405_needs_POST"]
        ]);
        die;
    }

    if ($isUser){
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
        echo json_encode([
            "code" => "you_are_already_logged_in",
            "message" => $dictionary["you_are_already_logged_in"]
        ]);
        die;
    }

    require "apis/profile/sign-up.php";
    return;
}

/* POST  login */
if ($_path[0] == "login"){
    if ($_method != "POST"){
        header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed", true, 405);
        echo json_encode([
            "code" => "405_needs_POST",
            "message" => $dictionary["405_needs_POST"]
        ]);
        die;
    }

    if ($isUser){
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
        echo json_encode([
            "code" => "you_are_already_logged_in",
            "message" => $dictionary["you_are_already_logged_in"]
        ]);
        die;
    }

    require "apis/profile/login.php";
    return;
}

/* 500 */
header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
echo json_encode([
    "code" => "500",
    "message" => $dictionary["500"]
]);
die;
