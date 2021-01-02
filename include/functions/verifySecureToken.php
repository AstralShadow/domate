<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Identification\Session as Session;

function verifySecureToken(Session $session, string $name, string $token) {
    $tokens = $session->tokens;
    $session->remove("tokens." . $name);
    return isset($tokens[$name]) && $token === $tokens[$name];
}
