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
class Division implements Interpreter\Operation
{

    /**
     * @param type $args
     * @return float
     */
    public function execute(...$args): float {
        if (count($args) != 2 || !is_numeric($args[0]->value) || !is_numeric($args[1]->value))
            throw new Interpreter\OperationException
                ("Division requires exactly 2 numeric parameters.");

        if (!floatval($args[1]->value)){
            throw new Interpreter\OperationException
                ("You're not allowed to divide by 0.");
        }

        return floatval($args[0]->value) / floatval($args[1]->value);
    }

}
