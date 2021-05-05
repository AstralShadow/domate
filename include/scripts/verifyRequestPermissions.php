<?php

/*
 * Returns 403 if api is in exclusive for other cathegory
 * If requesting forbidden page, redirects to home. 
 */


if (!defined("resolvedURI")){
    return;
}

if (!file_exists("data/accessPermissions.json")){
    return;
}

$is_user = isset($user);
$is_api = isset($_requested_resource["api"]);
$permissions = json_decode(file_get_contents("data/accessPermissions.json"), true);

if ($is_api){
    $api = $_requested_resource["api"];
    $isForbidden = false;

    if (!$is_user && in_array($api, $permissions["userApis"])){
        $isForbidden = true;
    }

    if ($isForbidden){
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 403);
        echo json_encode([
            "code" => "403",
            "message" => $dictionary["403"]
        ]);
        die;
    }

    unset($api, $isForbidden);
}

if (!$is_api){
    $page = $_requested_resource["page"];

    if (!$is_user && in_array($page, $permissions["userPages"])){
        $_requested_resource["page"] = $permissions["guestHome"];
    }

    if ($is_user && in_array($page, $permissions["guestPages"])){
        $_requested_resource["page"] = $permissions["userHome"];
    }

    unset($page);
}

unset($is_user, $is_api, $permissions);
