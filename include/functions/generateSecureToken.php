<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined("FUNC_GENERATE_SECURE_TOKEN")){
    define("FUNC_GENERATE_SECURE_TOKEN", true);

    function generateSecureToken(\Main\Session $session, string $name) {
        $token = base64_encode(random_bytes(36));
        $session->update([
            '$set' => [
                "tokens." . $name => $token
            ]
        ]);
        return $token;
    }

}