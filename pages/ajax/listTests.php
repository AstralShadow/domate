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
 * Action
 */

$tests = $user->tests ?? [];
foreach ($tests as $key => $value){
    $tests[$key] = (string) $value;
}


$response["msg"] = $dictionary->success;
$response["result"] = $tests;
$response["code"] = "Success";

unset($tests);
