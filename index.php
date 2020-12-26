<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Core elements */
require "vendor/autoload.php";
require "include/db.php";

/* Authentication */
require "include/session.php";
require "include/user.php";
require "include/scripts/authorizeUserBySession.php";

/* Contents */
require "include/scripts/includePage.php";
