<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined("LOADED_USER_DATA")) {
    define("LOADED_USER_DATA", true);
    require "include/classes/User.php";
    $user = new \Main\User();
}