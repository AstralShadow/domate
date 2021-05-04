<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * This file defines $whitebell variable based on socket location.
 * WhiteBell is an event-transfer-server software.
 * Very usefyl in Server-Send-Events based web applications.
 */

include "submodules/WhiteBellPHPClient/WhiteBellClient.php";

use WhiteBell\WhiteBellClient;

$whiteBellLocation = trim(file_get_contents("data/private/whitebell.location"));

if (!file_exists($whiteBellLocation)){
    unset($whiteBellLocation);
    $whitebell = null;
    return;
}

$whitebell = new WhiteBellClient($whiteBellLocation, "DomaTe");
unset($whiteBellLocation);
