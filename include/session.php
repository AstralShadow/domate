<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require "include/db.php";
if (!defined("SESSION_COOKIE") && isset($db)){
    define("SESSION_COOKIE", "MathSession");
    require "include/classes/Session.php";
    $session = new Main\Session($db);
}

/*
 * Usage:
 * $session->get(string $key);
 * $session->set(string $key, mixed $value);
 */
