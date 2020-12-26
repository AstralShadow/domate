<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/session.php";
require "include/user.php";

if (!defined("AUTHORIZED_USER_BY_SESSION")){
    if ($session->user === null)
        return;

    $user = \Main\User::fromSession($db, $session);
    define("AUTHORIZED_USER_BY_SESSION", true);
}

