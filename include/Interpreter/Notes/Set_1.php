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
class Set
{

    /** Float numbers */
    public array $_numbers = [];

    /** Sets of numbers [start, end, step] */
    public array $_sets = [];

    public function __construct(float $start, float $end, float $step = 1) {
        $this->includeSet($start, $end, $step);
    }

    /* Include/Exclude functions */

    /**
     * Addes a variable to the Range
     * @param float $value
     * @return void
     */
    public function includeValue(float $value): void {
        if (!in_array($value, $this->_numbers))
            $this->_numbers[] = $value;
    }

    /**
     * Addes a range, if step is 0, and set if step is positive
     * @param float $start
     * @param float $end
     * @param float $step
     * @return void
     */
    public function includeSet(float $start, float $end, float $step = 1): void {
        $this->checkInput($start, $end, $step);
        $this->_sets[] = [$start, $end, $step];
        // TODO: check for overlapping
    }

    /**
     * Throws error if the input is not correct
     * Conditions are:
     * $start < $end
     * $step > 0
     * @param float $start
     * @param float $end
     * @param float $step
     * @return void
     * @throws ParseException
     */
    private function checkInput(float $start, float $end, float $step): void {
        if ($start >= $end)
            throw new ParseException
                ("The start value must be smaller than the end value.");

        if ($step < 0)
            throw new ParseException
                ("The step must be positive.");

        if ($step === 0)
            throw new ParseException
                ("You should use range, not set.");

        if (abs($start) == INF || abs($end) == INF)
            throw new ParseException
                ("Infinity not implemented yet.");
    }

    /**
     * Removes a single value
     * Will unset it from _numbers if existing
     * Will devide any set if it contains the number
     * Will move sets with 1 value to _numbers
     * @param float $value
     * @return void
     */
    public function excludeValue(float $number): void {
        while (in_array($number, $this->_numbers))
            unset($this->_numbers[array_search($value, $this->_numbers)]);

        foreach ($this->_sets as $key => $set){
            if ($number < $set[0] || $number > $set[1])
                continue;

            $amplitude = $set[1] - $set[0];
            $difference = $number - $set[0];
            if ($difference % $set[2])
                continue;

            $position = $difference / $set[2];
            if ($position == 0){
                $set[0] += $set[2];
            }else if ($position == $amplitude / $set[2]){
                $set[1] -= $set[2];
            }else{
                $newSet = [$number + $set[2], $set[1], $set[2]];
                $set[1] = $number - $set[2];
                if ($newSet[0] == $newSet[1])
                    $this->_numbers[] = $newSet[0];
                else
                    $this->_sets[] = $newSet;
            }
            if ($set[0] == $set[1]){
                $this->_numbers[] = $set[0];
                unset($this->_sets[$key]);
            }
        }
    }

    /**
     * Runs excludeValue for each value in given set
     * Can be heavy, don't run with very small $step
     * @param float $start
     * @param float $end
     * @param float $step
     * @return void
     * @throws Exception
     */
    public function excludeRange(float $start, float $end, float $step = 0): void {
        $this->checkInput($start, $end, $step);
        for ($i = $start; $i <= $end; $i += $step)
            $this->excludeValue($i);
    }

    /* Contains functions */

    public function contains(float $number): bool {
        if (in_array($number, $this->_numbers))
            return true;
        foreach ($this->_sets as $set){
            if ($number < $set[0] || $number > $set[1])
                continue;

            $amplitude = $number - $set[0];
            if ($amplitude % $set[2])
                continue;

            return true;
        }
        return false;
    }

    public function containsLargerThan(float $number): bool {
        foreach ($this->_numbers as $num){
            if ($number < $num)
                return true;
        }

        foreach ($this->_sets as $set){
            if ($number < $set[1])
                return true;
        }
        return false;
    }

    public function containsSmallerThan(float $number): bool {
        foreach ($this->_numbers as $num){
            if ($number > $num)
                return true;
        }

        foreach ($this->_sets as $set){
            if ($number > $set[0])
                return true;
        }
        return false;
    }

    /**
     * Is the set empty
     * @param float $number
     * @return bool
     */
    public function isEmpty(): bool {
        $this->rationalize();
        return count($this->_include) === 0;
    }

    /* Actions with other Range */

    public function intersecton(Set $range): bool {
        throw new Exception('Not implemented');
    }

    public function unification(Set $range): bool {
        throw new Exception('Not implemented');
    }

}
