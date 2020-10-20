<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main;

/**
 * Description of User
 *
 * @author azcraft
 */
class User {

    //put your code here
    //-Sure.
    public function __construct() {
        global $session;
        $name = $session->get("user");
        if ($name == null)
            return;
    }

}
