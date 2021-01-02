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
     * Returns Interpreter\TYPE_NUMBER
     * @param type $args
     * @return type
     * @throws Interpreter\OperationException
     */
    public static function getType(...$args) {
        if (count($args) < 1)
            throw new Interpreter\OperationException
                ("Addition requires at least one parameter!");

        $exceptionMessages = [
            Interpreter\TYPE_STRING => "Addition can't work with strings!",
            Interpreter\TYPE_RANGE => "Addition of ranges not implemented!",
            Interpreter\TYPE_SET => "Addition of sets not implemented!",
            Interpreter\TYPE_ARRAY => "Addition of arrays not implemented!",
            Interpreter\TYPE_FUNCTION => "Addition of functions not implemented!"
        ];

        foreach ($args as $arg){
            if (isset($exceptionMessages[$arg->type]))
                throw new Interpreter\OperationException
                    ($exceptionMessages[$arg->type]);
        }

        return Interpreter\TYPE_NUMBER;
    }

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
