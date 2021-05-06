<?php

use Identification\User as User;

/* Input */
$credentials = [];
$required = ["user", "pwd"];
foreach ($required as $key){
    if (isset($_input[$key]) && is_string($_input[$key])){
        $credentials[$key] = trim($_input[$key]);
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
        echo json_encode([
            "code" => "missing_fields",
            "required" => $required,
            "message" => $dictionary["missing_fields"]
        ]);
        die;
    }
}

/* Login */
$errorByte = 0;
$user = User::authorize($db, $session, $credentials, $errorByte);

/* Failed */
if ($errorByte !== 0){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "failed",
        "message" => User::getErrorMessage($dictionary, $errorByte)
    ]);
    return;
}

/* Success */
header($_SERVER["SERVER_PROTOCOL"] . " 200 Success", true, 200);
echo json_encode([
    "code" => "success",
    "message" => $dictionary["login_messages"]["success"]
]);
return;
