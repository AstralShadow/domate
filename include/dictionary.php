<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/session.php";

if (!defined("DICTIONARY_LANGUAGE")){
    $lang = $session->lang ?? "bg_BG";

    require "include/MathExam/Dictionary.php";
    define("DICTIONARY_LANGUAGE", $lang);
    $dictionary = new MathExam\Dictionary($lang);

    unset($lang);
}
