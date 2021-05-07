<?php

// TODO provide good responses

require "include/dictionary.php";
require "include/secureTokens.php";

use MongoDB\BSON\ObjectId;
use MathExam\ActiveTest as ActiveTest;
use MathExam\TestSolution as TestSolution;
use MathExam\ExerciseVariant as ExerciseVariant;

$_method;
$_path;
$_input;

if (count($_path) == 0){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 404);
    die;
}

if (count($_path) > 0){
    if ($_path[0] == "get-token"){
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        echo json_encode([
            "code" => "token",
            "token" => generateSecureToken($session, "solution")
        ]);
        die;
    }

    $_exam_solution_id = trim($_path[0]);
    if (!TestSolution::exists($db, new ObjectId($_exam_solution_id))){
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
        die;
    }
    $_exam_solution = new TestSolution($db, new ObjectId($_exam_solution_id));

    $_active_exam_id = $_exam_solution->collection;
    if (!isset($_active_exam_id) || !ActiveTest::exists($db, new ObjectId($_active_exam_id))){
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
        die;
    }
    $_active_exam = new ActiveTest($db, new ObjectId($_active_exam_id));

    if ($_active_exam->teacher != $user->user){
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
        die;
    }
}

if (count($_path) > 1 && isset($_exam_solution)){
    $_question_id = trim($_path[1]);
    if (!in_array($_question_id, $_exam_solution->tasks)){
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
        die;
    }
    if (!ExerciseVariant::exists($db, new ObjectId($_question_id))){
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
        die;
    }
    $_question = new ExerciseVariant($db, new ObjectId($_question_id));
}

// $_exam;
// $_active_exam;
$_exam_solution;
$_question;

if (!isset($_question) && $_method == "GET"){
    require "apis/solution/get.php";
    return;
}

if (isset($_question) && $_method == "GET"){
    require "apis/solution/question.php";
    return;
}


if (!isset($_input["token"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "missing_token",
        "message" => $dictionary["missing_token"],
        "hint" => $dictionary["missing_token_hint"]
    ]);
    die;
}
if (!verifySecureToken($session, "solution", $_input["token"])){
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request", true, 400);
    echo json_encode([
        "code" => "invalid_token",
        "message" => $dictionary["invalid_token"],
        "hint" => $dictionary["missing_token_hint"]
    ]);
    die;
}
unset($_input["token"]);

if (isset($_question) && $_method == "PUT"){
    require "apis/solution/mark.php";
    return;
}


header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
echo json_encode([
    "code" => "404",
    "message" => $dictionary["404"]
]);
die;
