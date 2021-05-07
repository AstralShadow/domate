<?php

include "include/testsAndTasks.php";

$_exam;
$_active_exam;
$_exam_solution;
$_question;

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
echo json_encode($_question->dump());
return;
