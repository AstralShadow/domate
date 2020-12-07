<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/db.php";

if (!defined("CLASS_TASK")){
    define("CLASS_TASK", true);
    require "include/classes/Interpreter/Task.php";

    /**
     * Loads task from database, returns null if not defined
     * Wrapper for Task.load($id)
     * @global type $db
     * @param \MongoDB\BSON\ObjectId $id
     * @return \Main\Task|null $task
     */
    function loadTask(\MongoDB\BSON\ObjectId $id): ?\Main\Interpreter\Task {
        global $db;
        if (!isset($db))
            return null;
        $task = new Task();
        if ($task->load($id))
            return $task;
        return null;
    }

}