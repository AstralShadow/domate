<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/db.php";
require "include/shared.php";

/**
 * @global Identification\Session $session
 */
if (!defined("SESSION_COOKIE") && defined("DEFINED_DB_CLIENT")){
    define("SESSION_COOKIE", "MathSession");
    require "include/Identification/Session.php";

    $session = new Identification\Session($db);
}
