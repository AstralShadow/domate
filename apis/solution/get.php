<?php

include "include/testsAndTasks.php";

$_exam_solution;

if (false === strpos($_SERVER["HTTP_ACCEPT"], "text/event-stream")){
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
    echo json_encode([
        "code" => "success",
        "data" => $_exam_solution->getDataForTeacher(),
        "message" => $dictionary["success"]
    ]);
    return;
}


header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented", true, 503);
echo json_encode(["note" => "use tracking of ActiveTest instead."]);
die;
