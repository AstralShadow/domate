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
class Addition implements Interpreter\Operation
{

    /**
     * @param type $args
     * @return float
     */
    public function execute(...$args): float {
        if (count($args) < 1)
            throw new Interpreter\OperationException
                ("Addition requires at least one parameter!");

        $result = 0;
        foreach ($args as $var){
            if (!is_numeric($var->value))
                throw new Interpreter\OperationException
                    ("Addition can't work with non-numeric parameters!");
            $result += floatval($var->value);
        }

        return $result;
    }

}
