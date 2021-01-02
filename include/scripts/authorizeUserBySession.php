<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/session.php";
require "include/user.php";

/**
 * @global null|Identification\User $user
 */
if (!defined("AUTHORIZED_USER_BY_SESSION")){
    if ($session->user !== null){
        define("AUTHORIZED_USER_BY_SESSION", true);
        $user = Identification\User::fromSession($db, $session);
    }
}

