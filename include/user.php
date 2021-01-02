<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/db.php";
require "include/shared.php";

if (!defined("LOADED_USER_DATA")){
    define("LOADED_USER_DATA", true);
    define("USER_ERRCODE_NO_ERROR", 0);
    define("USER_ERRCODE_USER_ALREADY_EXIST", 1);
    define("USER_ERRCODE_USER_DOES_NOT_EXIST", 2);
    define("USER_ERRCODE_ILLEGAL_USERNAME", 4);
    define("USER_ERRCODE_ILLEGAL_PASSWORD", 8);
    define("USER_ERRCODE_WRONG_PASSWORD", 16);

    require "include/Identification/User.php";
    unset($i);
}