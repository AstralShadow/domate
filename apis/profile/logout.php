<?php

$user->logout();

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "success",
    "message" => $dictionary["logout_message"]
]);
return;
