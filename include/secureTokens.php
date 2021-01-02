<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/session.php";

if (!defined("FUNC_SECURE_TOKENS")){
    define("FUNC_SECURE_TOKENS", true);

    require "include/functions/generateSecureToken.php";
    require "include/functions/verifySecureToken.php";
}
