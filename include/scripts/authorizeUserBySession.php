<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/session.php";
require "include/user.php";

use Identification\User;

/**
 * @global null|Identification\User $user
 */
if (!defined("USER_AUTHORIZED")){
    if ($session->user !== null){
        define("USER_AUTHORIZED", true);
        $user = User::fromSession($db, $session);
    }
}

