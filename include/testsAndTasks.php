<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/user.php";

if (!defined("CLASS_TEST")){
    define("CLASS_TEST", true);

    require "include/MathExam/Exercise.php";
    require "include/MathExam/ExerciseGroup.php";
    require "include/MathExam/Test.php";

    require "include/MathExam/ActiveTest.php";
    require "include/MathExam/TestSolution.php";
    require "include/MathExam/ExerciseVariant.php";
    require "include/MathExam/TestVariantGenerator.php";
}
