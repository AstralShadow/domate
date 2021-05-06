<?php

include "include/testsAndTasks.php";

use MongoDB\BSON\ObjectId;

$remove_user_access = [
    '$pull' => [
        "activeTests" => new ObjectId($_id2)
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
    $whitebell->dispatchEvent("deleted_active_exam_" . $user->id, $_id2);
}

return;
