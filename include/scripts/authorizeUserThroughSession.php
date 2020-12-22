<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/session.php";
require "include/user.php";

if (!defined("AUTHORIZED_USER_THROUGH_SESSION")){
    if (!isset($session->user))
        return;

    $user = User::fromSession($this->database, $session);
    defined("AUTHORIZED_USER_THROUGH_SESSION", true);
}
