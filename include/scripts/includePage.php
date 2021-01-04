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
    if (isset($_GET['p']) && is_string($_GET['p'])){
        if (preg_match("/^[a-zA-Z0-9\_]{5,}$/", $_GET['p'])){
            $page = $_GET['p'];
        }
    }

    $dirs = ["content", "standalone", "ajax", "test", "files"];

    foreach ($dirs as $dir){
        if (in_array($page . ".php", scandir("pages/" . $dir))){
            $pageType = $dir;
        }
    }
    unset($dirs);

    if (!isset($pageType)){
        $pageType = "standalone";
        $page = "home";
    }
}

if (!defined("CHECKED_ACCESS_PERMISSIONS") && defined("USER_AUTHORIZED")){
    define("CHECKED_ACCESS_PERMISSIONS", true);

    $defaultGuestPage = "home";
    $userOnlyPages = [];

    $defaultUserPage = "tests";
    $guestOnlyPages = [];

    if (file_exists("data/accessPermissions.json")){
        $raw = file_get_contents("data/accessPermissions.json");
        $data = json_decode($raw, 1);

        $defaultGuestPage = $data["defaultGuestPage"];
        $userOnlyPages = $data["userOnlyPages"];
        $defaultUserPage = $data["defaultUserPage"];
        $guestOnlyPages = $data["guestOnlyPages"];

        unset($raw, $data);
    }


    $forwardTo = null;

    if (in_array($page, $userOnlyPages) && !isset($user)){
        $forwardTo = $defaultGuestPage;
    } else if (in_array($page, $guestOnlyPages) && isset($user)){
        $forwardTo = $defaultUserPage;
    }

    if (isset($forwardTo)){
        if ($pageType === "ajax"){
            echo json_encode([
                "msg" => $dictionary->forbidden,
                "code" => "Forbidden",
                "reload" => false
            ]);
            unset($response);

            $page = null;
            $pageType = null;
        } else{
            $page = $forwardTo;
            $pageType = "standalone";
        }
    }

    unset($defaultGuestPage, $userOnlyPages, $defaultUserPage, $guestOnlyPages, $forwardTo);
}

if (!defined("INCLUDED_PAGE")){
    define("INCLUDED_PAGE", true);

    switch ($pageType){
        case "file":
            include "pages/files/" . $page . ".php";
            break;

        case "ajax":
            $response = [
                "msg" => $dictionary->formMessages["noAction"],
                "code" => null,
                "reload" => false
            ];
            include "pages/ajax/" . $page . ".php";

            echo json_encode($response);
            unset($response);
            break;

        case "test":
            include "pages/test/" . $page . ".php";
            break;

        case "content":
            include "pages/templates/default.php";
            break;

        case "standalone":
            include "pages/standalone/" . $page . ".php";
            break;
    }
    unset($pageType, $page);
}