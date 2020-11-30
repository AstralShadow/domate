<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main;

/**
 * Description of Task
 *
 * @author azcraft
 */
class Task
{

    /**
     * Loads a task from the database
     * @param \MongoDB\BSON\ObjectId $id
     * @return bool $success
     */
    public function load(\MongoDB\BSON\ObjectId $id): bool {
        global $db;
    }

}
