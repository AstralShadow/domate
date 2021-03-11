<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @global \MongoDB\Client|null $db
 */
if (!defined("DEFINED_DB_CLIENT")){

    // Default arguments
    $args = [
        "url" => "mongodb://localhost:27017",
        "user" => "test",
        "pwd" => "test",
        "db" => "test"
    ];

    // Loading configuration file
    if (file_exists("data/private/mongodb_authentication.json")){
        $options = json_decode(file_get_contents("data/private/mongodb_authentication.json"), true);
        foreach (array_keys($args) as $key){
            if (isset($options[$key]) && is_string($options[$key])){
                $args[$key] = $options[$key];
            }
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

        // To trigger an error if the authorization is wrong.
        $dbClient->listDatabases();
        define("DEFINED_DB_CLIENT", true);
    } catch (Exception $e){
        header("HTTP/1.1 500 Internal Server Error", true, 500);
        throw $e;
    }

    unset($args, $dbClient, $k);
}


