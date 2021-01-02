<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter;

/**
 * A range includes every value between $start and $end.
 * $start and $end are not included.
 * Operations over variables are to be tracked in case $start or $end are INF
 *
 * @author azcraft
 */
class Range
{

    public float $start;
    public float $end;
    public float $startOperationTracker = 1;
    public float $endOperationTracker = 1;

    public function __construct($start, $end) {
        if ($start >= $end)
            throw new ParseException
                ("The start value must be smaller than the end value.");

        $this->start = $start;
        $this->end = $end;
    }

    public function contains(float $number) {
        return $this->start < $number && $number < $this->end;
    }

    public function containsLargerThan(float $number): bool {
        return $this->end > $number;
    }

    public function containsSmallerThan(float $number): bool {
        return $this->start < $number;
    }

    public function isEmpty(): bool {
        return $this->start >= $this->end;
    }

    /* Actions with other Range */

    public function intersecton(Set $range) {
        throw new Exception('Not implemented');
    }

    public function unification(Set $range) {
        throw new Exception('Not implemented');
    }

}
