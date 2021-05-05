<?php

use Identification\User as User;

/* Input */
$data = [];
$required = ["user", "pwd", "pwd2"];
foreach ($required as $key){
    if (isset($_input[$key]) && is_string($_input[$key])){
        $data[$key] = trim($_input[$key]);
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 404);
        echo json_encode([
            "code" => "missing_fields",
            "required" => $required,
            "message" => $dictionary["missing_fields"]
        ]);
        die;
    }
}

if ($data["pwd"] !== $data["pwd2"]){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 404);
    echo json_encode([
        "code" => "different_passwords",
        "required" => $required,
        "message" => $dictionary["sign_up_messages"]["different_passwords"]
    ]);
    die;
}

if (file_exists("data/commonPasswords.json")){
    $common_passwords = json_decode(file_get_contents("data/commonPasswords.json"), true);
    if (in_array($data["pwd"], $common_passwords)){
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 404);
        echo json_encode([
            "code" => "password_already_used",
            "required" => $required,
            "message" => $dictionary["sign_up_messages"]["password_already_used"]
        ]);
        die;
    }
}


unset($data["pwd2"]);
$errorByte = 0;
User::create($db, $data, $errorByte);
unset($userData);

if ($errorByte !== 0){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 404);
    echo json_encode([
        "code" => "failed",
        "message" => User::getErrorMessage($dictionary, $errorByte)
    ]);
    die;
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 404);
echo json_encode([
    "code" => "success",
    "message" => $dictionary["sign_up_messages"]["success"]
]);
die;
