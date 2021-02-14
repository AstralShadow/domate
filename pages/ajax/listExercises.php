<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Note: front-end respects array order
 */

/*
 * Lists accessible exercises
 * 
 * User only page
 */

$exercises = $user->exercises ?? [];
foreach ($exercises as $key => $value){
    $exercises[$key] = (string) $value;
}


$response["msg"] = $dictionary->success;
$response["result"] = $exercises;
$response["code"] = "Success";
