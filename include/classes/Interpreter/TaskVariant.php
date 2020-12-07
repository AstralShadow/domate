<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter;

/**
 * Represents a single version of a Task, with solution.
 *
 * @author azcraft
 */
class TaskVariant
{

    public $answer = null;
    public string $description = "";
    public $variables = [];
    public $original;

    /**
     * Loads a taskVariant from the database
     * @param \MongoDB\BSON\ObjectId $id
     * @return bool $success
     */
    public function load(\MongoDB\BSON\ObjectId $id): bool {
        global $db;
    }

}
