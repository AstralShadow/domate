<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter;

if (!defined("CLASS_TASK_INTERPRETER")){
    define(__NAMESPACE__ . "\TYPE_NUMBER", 1);
    define(__NAMESPACE__ . "\TYPE_POTENTIAL_NUMBER", 2);
    define(__NAMESPACE__ . "\TYPE_STRING", 4);
    define(__NAMESPACE__ . "\TYPE_ARRAY", 16);
    define(__NAMESPACE__ . "\TYPE_FUNCTION", 64);

    require "include/classes/Interpreter/PotentialNumber.php";
}
