<?php

if (defined("GENERATE_SECURE_TOKEN_INCLUDED")){
    return;
}
define("GENERATE_SECURE_TOKEN_INCLUDED", true);

use Identification\Session as Session;

/**
 * Generates and records in a session a random token.
 * @param Session $session
 * @param string $name
 * @return string $token
 */
function generateSecureToken(Session $session, string $name): string {
    $token = base64_encode(random_bytes(36));
    $session->update([
        '$set' => [
            "tokens." . $name => $token
        ]
    ]);
    return $token;
}
