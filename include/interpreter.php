<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/task.php";

if (!defined("CLASS_TASK_INTERPRETER")){
    require "include/Interpreter/variableTypes.php";

    require "include/Interpreter/TaskVariant.php";
    require "include/Interpreter/Interpreter.php";
    require "include/Interpreter/Parsers.php";
    require "include/Interpreter/Variable.php";
    require "include/Interpreter/Expression.php";

    require "include/Interpreter/Operation.php";
    require "include/Interpreter/Operations/MathematicOperation.php";
    // require "include/Interpreter/Operations/Exponentiation.php";
    // require "include/Interpreter/Operations/Multiplication.php";
    // require "include/Interpreter/Operations/Division.php";
    // require "include/Interpreter/Operations/Modulo.php";
    require "include/Interpreter/Operations/Addition.php";
    require "include/Interpreter/Operations/Subtraction.php";

    require "include/Interpreter/Exceptions/ParseException.php";
    require "include/Interpreter/Exceptions/OperationException.php";
    define("CLASS_TASK_INTERPRETER", true);
}