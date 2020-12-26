<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter\Operations;

use \Main\Interpreter as Interpreter;

/**
 * Description of Expression
 *
 * @author azcraft
 */
abstract class MathematicOperation extends Interpreter\Operation
{

    public static array $accepts = [
        Interpreter\TYPE_NUMBER,
        Interpreter\TYPE_POTENTIAL_NUMBER
    ];

    /**
     * Returns Interpreter\TYPE_NUMBER or Interpreter\TYPE_RANGE
     * @param type $args
     * @return type
     * @throws Interpreter\OperationException
     */
    public function getType(...$args) {
        if (count($args) < 1)
            throw new Interpreter\OperationException
                    ("Operation requires at least one parameter!");

        $type = Interpreter\TYPE_NUMBER;
        foreach ($args as $arg){
            if (isset($exceptionMessages[$arg->type]))
                throw new Interpreter\OperationException
                        ("Operation not supported with that kind of arguments");
            if ($arg->type === Interpreter\TYPE_POTENTIAL_NUMBER)
                $type = Interpreter\TYPE_POTENTIAL_NUMBER;
        }

        return $type;
    }

    /**
     * Executes operation
     * Recursively for 2 elements until only one is left.
     * Calls functions:
     * numbers for 2 numbers
     * sets for 2 sets
     * setAndNumber for set and number
     * @param type $args
     * @return type
     */
    public function execute(...$args) {
        for ($i = 0; $i < count($args) - 1; $i++){
            $arg1 = $args[$i];
            $arg2 = $args[$i + 1];
            $type1 = $this->getType($arg1);
            $type2 = $this->getType($arg2);
            if ($type1 == $type2){
                /* 2 numbers */
                if ($type1 == Interpreter\TYPE_NUMBER)
                    $args[$i + 1] = $this->numbers($arg1->value, $arg2->value);

                /* 2 sets */
                if ($type1 == Interpreter\TYPE_POTENTIAL_NUMBER)
                    $args[$i + 1] = $this->potentials($arg1->value, $arg2->value);
                continue;
            }
            /* Set and Number */
            if ($type1 == Interpreter\TYPE_POTENTIAL_NUMBER && $type2 == Interpreter\TYPE_NUMBER){
                $this->potentialAndNumber($arg1->value, $arg2->value);
                continue;
            }
            /* Number and Set */
            if ($type1 == Interpreter\TYPE_NUMBER && $type2 == Interpreter\TYPE_POTENTIAL_NUMBER){
                $this->potentialAndNumber($arg2->value, $arg1->value);
                continue;
            }
        }
        return $args[count($args) - 1];
    }

    protected abstract function numbers(float $a, float $b): float;

    protected abstract function potentials(Interpreter\PotentialNumber $a, Interpreter\PotentialNumber $b): Interpreter\PotentialNumber;

    protected abstract function potentialAndNumber(Interpreter\PotentialNumber $a, float $b): Interpreter\PotentialNumber;
}
