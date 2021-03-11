<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
