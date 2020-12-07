<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter\Operations;

use \Main\Interpreter as Interpreter;

/**
 * Arithmetic division.
 *
 * @author azcraft
 */
class Subtraction implements Interpreter\Operation
{

    /**
     * @param type $args
     * @return float
     */
    public function execute(...$args): float {
        if (count($args) != 2 || !is_numeric($args[0]->value) || !is_numeric($args[1]->value))
            throw new Interpreter\OperationException
                ("Subtraction requires exactly 2 numeric parameters.");

        return floatval($args[0]->value) - floatval($args[1]->value);
    }

}
