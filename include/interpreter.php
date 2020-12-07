<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/task.php";

if (!defined("CLASS_TASK_INTERPRETER")){
    define("CLASS_TASK_INTERPRETER", true);
    require "include/classes/Interpreter/TaskVariant.php";
    require "include/classes/Interpreter/Interpreter.php";
    require "include/classes/Interpreter/Parsers.php";
    require "include/classes/Interpreter/Variable.php";
    require "include/classes/Interpreter/Expression.php";

    require "include/classes/Interpreter/Operation.php";
    require "include/classes/Interpreter/Operations/Exponentiation.php";
    require "include/classes/Interpreter/Operations/Multiplication.php";
    require "include/classes/Interpreter/Operations/Division.php";
    require "include/classes/Interpreter/Operations/Modulo.php";
    require "include/classes/Interpreter/Operations/Addition.php";
    require "include/classes/Interpreter/Operations/Subtraction.php";

    require "include/classes/Interpreter/ParseException.php";
    require "include/classes/Interpreter/OperationException.php";
}