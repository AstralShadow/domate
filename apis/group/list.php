<?php

if (false === strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    $groups = $user->exerciseGroups ?? [];
    foreach ($groups as $key => $value){
        $groups[$key] = (string) $value;
    }

    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "data" => $groups,
        "message" => $dictionary["success"]
    ]);

    return;
}

require "include/whiteBell.php";

if (!isset($whitebell)){
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable", true, 503);
    die;
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$whitebell->addEventListener("new_group_" . $user->id, function (string $id){
    echo "event: new_group\n";
    echo 'data: {"id": "' . $id . '"}';
    echo "\n\n";
});

$whitebell->addEventListener("deleted_group_" . $user->id, function (string $id){
    echo "event: deleted_group\n";
    echo 'data: {"id": "' . $id . '"}';
    echo "\n\n";
});

if (!defined("DONT_RUN_WHITEBELL")){
    $whitebell->run();
}
