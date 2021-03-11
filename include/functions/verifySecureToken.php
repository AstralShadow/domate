<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Identification\Session as Session;

/**
 * Checks if the token is the same as recorded and deletes the token
 * @param Session $session
 * @param string $name
 * @param string $token
 * @return bool $valid 
 */
function verifySecureToken(Session $session, string $name, string $token): bool {
    $tokens = $session->tokens;
    if (!isset($tokens[$name])){
        return false;
    }

    $valid = $token === $tokens[$name];
    $session->remove("tokens." . $name);
    return $valid;
}
