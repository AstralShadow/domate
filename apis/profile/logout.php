<?php

$user->logout();

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 404);
echo json_encode([
    "code" => "success",
    "message" => $dictionary["logout_message"]
]);
