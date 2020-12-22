<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined("DEFINED_DB_CLIENT")){
    define("DEFINED_DB_CLIENT", true);

    // Default arguments
    $args = [
        "url" => "mongodb://localhost:27017",
        "user" => null,
        "pwd" => null,
        "db" => "test"
    ];

    // Loading configuration file
    if (file_exists("data/private/mongodb_authentication.json")){
        $options = json_decode(file_get_contents("data/private/mongodb_authentication.json"), true);
        foreach (array_keys($args) as $key){
            if (isset($options[$key]) && is_string($options[$key]))
                $args[$key] = $options[$key];
        }
        unset($options);
    }

    // Connecting to server
    try{
        $dbClient = new MongoDB\Client($args["url"], [
            "username" => $args["user"],
            "password" => $args["pwd"],
            "authSource" => $args["db"],
            "db" => $args["db"]
        ]);
        $k = $args["db"];
        $db = $dbClient->$k;
        $dbClient->listDatabases();
    }catch (Exception $e){
        if (!isset($_GET["mock"])){
            echo "Missing Database.";
            die;
        }
        $db = null;
    }

    unset($args, $dbClient, $k);
}

