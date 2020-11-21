<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined("LOADED_USER_DATA")) {
    define("LOADED_USER_DATA", true);
    $i = 0;
    define("USER_ERRCODE_NO_ERROR", $i++);
    define("USER_ERRCODE_USER_ALREADY_EXIST", $i++);
    define("USER_ERRCODE_USER_DOES_NOT_EXIST", $i++);
    define("USER_ERRCODE_ILLEGAL_USERNAME", $i++);
    define("USER_ERRCODE_ILLEGAL_PASSWORD", $i++);
    define("USER_ERRCODE_WRONG_PASSWORD", $i++);

    require "include/classes/User.php";
    $user = new \Main\User();
    unset($i);
}