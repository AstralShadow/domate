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
class Modulo implements Interpreter\Operation
{

    /**
     * @param type $args
     * @return float
     */
    public function execute(...$args): float {
        if (count($args) != 2 || !is_numeric($args[0]->value) || !is_numeric($args[1]->value))
            throw new Interpreter\OperationException
                ("Modulo requires exactly 2 numeric parameters.");

        if (!floatval($args[1]->value)){
            throw new Interpreter\OperationException
                ("You're not allowed to divide by 0, so you won't get modulo by 0.");
        }

        return self::fmod(floatval($args[0]->value), floatval($args[1]->value));
    }

    public static function fmod(float $a, float $b): float {
        $c = $a / $b;
        if (abs($c - round($c)) < PHP_FLOAT_EPSILON)
            $c = round($c);
        $c = $c > 0 ? floor($c) : ceil($c);
        return $a - $c * $b;
    }

}
