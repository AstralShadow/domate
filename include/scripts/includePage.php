<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/dictionary.php";

if (!defined("DETECTED_PAGE")){
    define("DETECTED_PAGE", true);

    /* Defaults */
    $page = "home";
    $pageType = null;

    /* Input */
    if (isset($_GET["test"]) && is_string($_GET["test"])){
        $page = "joinTest";
    } else if (isset($_GET['p']) && is_string($_GET['p'])){
        if (preg_match("/^[a-zA-Z0-9\_]{2,20}$/", $_GET['p'])){
            $page = $_GET['p'];
        }
    }

    /* Validation */
    $dirs = ["content", "standalone", "ajax", "test", "file", "sse"];
    foreach ($dirs as $dir){
        if (!is_dir("pages/" . $dir)){
            continue;
        }
        if (in_array($page . ".php", scandir("pages/" . $dir))){
            $pageType = $dir;
            break;
        }
    }
    unset($dirs, $dir);

    if (!isset($pageType)){
        $pageType = "standalone";
        $page = "home";
    }
}

if (!defined("CHECKED_ACCESS_PERMISSIONS") && defined("USER_AUTHORIZED") && defined("DETECTED_PAGE")){
    define("CHECKED_ACCESS_PERMISSIONS", true);

    /* Default values */
    $defaultGuestPage = "home";
    $userOnlyPages = [];

    $defaultUserPage = "tests";
    $guestOnlyPages = [];

    /* Config values */
    if (file_exists("data/accessPermissions.json")){
        $raw = file_get_contents("data/accessPermissions.json");
        $data = json_decode($raw, 1);

        $defaultGuestPage = $data["defaultGuestPage"];
        $userOnlyPages = $data["userOnlyPages"];
        $defaultUserPage = $data["defaultUserPage"];
        $guestOnlyPages = $data["guestOnlyPages"];

        unset($raw, $data);
    }

    /* Detect Forward Location */
    $forwardTo = null;
    if (in_array($page, $userOnlyPages) && !isset($user)){
        $forwardTo = $defaultGuestPage;
    } else if (in_array($page, $guestOnlyPages) && isset($user)){
        $forwardTo = $defaultUserPage;
    }
    unset($defaultGuestPage, $userOnlyPages, $defaultUserPage, $guestOnlyPages);

    /* Custom operations */
    if (isset($forwardTo) && $pageType === "ajax"){
        header("HTTP/1.1 403 Forbidden", true, 403);
        echo json_encode([
            "msg" => $dictionary->forbidden,
            "code" => "Forbidden",
            "reload" => false
        ]);
        unset($page, $pageType, $forwardTo);
        return;
    }

    /* Default operation */
    if (isset($forwardTo)){
        $page = $forwardTo;
        $pageType = "standalone";
    }
    unset($forwardTo);
}

if (!defined("INCLUDED_PAGE")){
    define("INCLUDED_PAGE", true);

    switch ($pageType){
        /* Default actions */
        default: /* Not Found */
            header("HTTP/1.1 404 Not Found", true, 404);
            break;

        case "file": /* Custom file transfers (To Do) */
        case "test": /* Test Files */
        case "standalone": /* Normal HTML (To Do) */
        case "sse": /* SSE API (In Progress) */
            include "pages/" . $pageType . "/" . $page . ".php";
            break;

        /* Template HTML (To Do) */
        case "content":
            include "pages/templates/default.php";
            break;

        /* AJAX API */
        case "ajax":
            /* Init */
            $input = null;
            $method = 'get';
            $contentType = null;
            $response = [
                "msg" => $dictionary->formMessages["noAction"],
                "code" => null,
                "reload" => false
            ];

            /* Input */
            if (is_string($_SERVER['REQUEST_METHOD'])){
                $method = $_SERVER['REQUEST_METHOD'];
                $contentType = trim($_SERVER["CONTENT_TYPE"] ?? '');
            }
            if ($method === 'POST' && $contentType === 'application/json'){
                $inputRaw = file_get_contents("php://input");
                $input = json_decode($inputRaw, true);
            }

            /* Action */
            $success = include "pages/ajax/" . $page . ".php";
            if ($success === false){
                header("HTTP/1.1 403 Forbidden", true, 403);
                echo json_encode([
                    "msg" => $dictionary->forbidden,
                    "code" => "Forbidden",
                    "reload" => false
                ]);
                break;
            }

            /* Output */
            echo json_encode($response);
            unset($method, $contentType);
            unset($input, $inputRaw);
            unset($success, $response);
            break;
    }
    unset($pageType, $page);
}  