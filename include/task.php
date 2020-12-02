<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/db.php";
if (!defined("CLASS_TASK")){
    define("CLASS_TASK", true);
    require "include/classes/Task.php";

    function loadTask(): \Main\Task {
        global $db;
        if (!isset($db))
            return new Task();
    }

}