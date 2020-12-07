<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter\Operations;

use \Main\Interpreter as Interpreter;

/**
 * Arithmetic multiplication.
 *
 * @author azcraft
 */
class Multiplication implements Interpreter\Operation
{

    /**
     * Multiplies N elements. One of them can be text xD
     * @param type $args
     * @return float
     */
    public function execute(...$args): float {
        if (count($args) < 1)
            throw new Interpreter\OperationException
                ("Multiplication requires at least one parameter!");

        $result = 1;
        foreach ($args as $var){
            if (!is_numeric($var->value))
                throw new Interpreter\OperationException
                    ("Multiplication can't work with non-numeric parameters!");
            $result *= floatval($var->value);
        }

        return $result;
    }

}
