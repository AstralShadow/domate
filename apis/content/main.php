<?php

require "include/dictionary.php";
require "include/secureTokens.php";

use MongoDB\BSON\ObjectId;
use MathExam\Test as Test;

$_method;
$_path;
$_input;

if (false !== strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    require "apis/content/all_content_events.php";
    return;
}



header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
echo json_encode([
    "code" => "404",
    "message" => $dictionary["404"]
]);
die;
