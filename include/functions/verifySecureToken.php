<?php

if (defined("VERIFY_SECURE_TOKEN_INCLUDED")){
    return;
}
define("VERIFY_SECURE_TOKEN_INCLUDED", true);

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
