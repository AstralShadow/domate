<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined("DETECTED_PAGE")){
    define("DETECTED_PAGE", true);
    $page = "home";
    $pageType = null;
    if (isset($_GET['p']) && is_string($_GET['p']))
        if (preg_match("/^[a-zA-Z0-9]{5,}$/", $_GET['p']))
            $page = $_GET['p'];

    $dirs = ["content", "standalone", "ajax", "test", "files"];

    foreach ($dirs as $dir)
        if (in_array($page . ".php", scandir("pages/" . $dir)))
            $pageType = $dir;
    unset($dirs);
}

if (!defined("CHECKED_PAGE_ACCESS_PERMISSIONS") && defined("LOADED_USER_DATA")){
    define("CHECKED_PAGE_ACCESS_PERMISSIONS", true);
}

if (!defined("INCLUDED_PAGE")){
    define("INCLUDED_PAGE", true);

    require "include/dictionary.php";
    switch ($pageType){
        case "file":
            include "pages/files/" . $page . ".php";
            break;

        case "ajax":
            include "pages/ajax/" . $page . ".php";
            break;

        case "test":
            include "pages/test/" . $page . ".php";
            break;

        case "content":
            include "pages/templates/default.php";
            break;

        default:
            $page = "home";
        case "standalone":
            include "pages/standalone/" . $page . ".php";
            break;
    }
    unset($pageType, $page);
}