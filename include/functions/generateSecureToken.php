<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Identification\Session as Session;

function generateSecureToken(Session $session, string $name) {
    $token = base64_encode(random_bytes(36));
    $session->update([
        '$set' => [
            "tokens." . $name => $token
        ]
    ]);
    return $token;
}
