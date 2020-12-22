<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter;

/**
 * Description of Variable
 *
 * @author azcraft
 */
class Variable
{

    /**
     * Value of the variable, if resolved
     * @var mixed|null
     */
    public $value;

    /**
     * Name of the varuable
     * @var string|null
     */
    public $name = null;

    /**
     * Is the variable resolved
     * @var bool 
     */
    public bool $resolved = false;

    /**
     * References to all variables we need to resolve this one.
     * Self-reference is possible.
     * system.set function ignores references.
     * @var Variable[] 
     */
    public $dependencies = [];
    public $type = null;

}
