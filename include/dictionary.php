<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/session.php";

if (!defined("DICTIONARY_LANGUAGE")){
    if (isset($session))
        $lang = $session->get("lang");
    if (!isset($lang))
        $lang = "bg_BG";

    require "include/classes/Dictionary.php";
    define("DICTIONARY_LANGUAGE", $lang);
    $dictionary = new Main\Dictionary($lang);

    unset($lang);
}
