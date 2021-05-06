<?php

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;

$remove_user_access = [
    '$pull' => [
        "exerciseGroups" => new ObjectId($_id)
    ]
];
$user->update($remove_user_access);

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode([
    "code" => "success",
    "message" => $dictionary["success"]
]);

require "include/whiteBell.php";
if (isset($whitebell)){
    $whitebell->dispatchEvent("deleted_group_" . $user->id, $_id);
}

return;
