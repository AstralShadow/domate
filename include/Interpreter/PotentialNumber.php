<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter;

/**
 * Description of Set
 *
 * @author azcraft
 */
class PotentialNumber
{

    public float $minValue;
    public float $maxValue;
    public array $steps = [];
    public int $type = TYPE_POTENTIAL_NUMBER;

    public function __construct(
        float $min, float $max, float $precision = 1) {
        if ($min >= $max)
            throw new ParseException
                ("The min value must be smaller than the max value.");

        if ($precision <= 0)
            throw new ParseException
                ("The precision must be positive.");

        $this->minValue = $min;
        $this->maxValue = $max;
        $this->steps[] = [$precision, ($max - $min) / $precision];
    }

    public function containsLargerThan(float $number): bool {
        return $this->maxValue > $number;
    }

    public function containsSmallerThan(float $number): bool {
        return $this->minValue < $number;
    }

    public function contains(float $number): bool {
        if ($number < $this->minValue || $number > $this->maxValue)
            return false;
        if ($number == $this->minValue || $number == $this->maxValue)
            return true;

        $requiredSum = $number - $this->minValue;

        $counts = $this->getStepsCounts($requiredSum);
        $values = $this->getStepsValues($requiredSum);
        if (count($counts) == 0){
            return false;
        }
        $used = [];
        for ($i = 0; $i < count($values); $i++)
            $used[] = 0;

        $sum = 0;
        $c = count($used) - 1;
        while ($used[$c] <= $counts[$c]){
            if (abs($requiredSum - $sum) < PHP_FLOAT_EPSILON)
                return true;

            $used[0]++;
            $sum += $values[0];
            for ($i = 0; $i < count($used) - 1; $i++){
                if ($used[$i] <= $counts[$i])
                    continue;

                $sum -= $values[$i] * $used[$i];
                $sum += $values[$i + 1];
                $used[$i] = 0;
                $used[$i + 1]++;
            }
        }
        return false;
    }

    private function getStepsValues(float $requiredSum) {
        $values = [];
        foreach ($this->steps as $step){
            $newStep = $this->usableStep($step, $requiredSum);
            if (isset($newStep))
                $values[] = $newStep[0];
        }
        return $values;
    }

    private function getStepsCounts(float $requiredSum) {
        $counts = [];
        foreach ($this->steps as $step){
            $newStep = $this->usableStep($step, $requiredSum);
            if (isset($newStep))
                $counts[] = $newStep[1];
        }
        return $counts;
    }

    private function usableStep($step, float $requiredSum) {
        if ($step[0] > $requiredSum)
            return null;
        $count = min($step[1], floor($requiredSum / $step[0]));
        if ($count == 0)
            return null;
        return [$step[0], $count];
    }

}
