<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter\Operations;

use \Main\Interpreter as Interpreter;

class Addition extends MathematicOperation
{

    protected function numbers(float $a, float $b): float {
        return $a + $b;
    }

    protected function potentialAndNumber(Interpreter\PotentialNumber $a, float $b): Interpreter\PotentialNumber {
        $a->minValue += $b;
        $a->maxValue += $b;
        return $a;
    }

    protected function potentials(Interpreter\PotentialNumber $a, Interpreter\PotentialNumber $b): Interpreter\PotentialNumber {
        $minValue = $a->minValue + $b->minValue;
        $maxValue = $a->maxValue + $b->maxValue;
        $potential = new Interpreter\PotentialNumber($minValue, $maxValue);
        $potential->steps = array_merge($a->steps, $b->steps);
        return $potential;
    }

}
