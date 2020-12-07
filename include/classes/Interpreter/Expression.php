<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter;

/**
 * Description of Expression
 *
 * @author azcraft
 */
class Expression extends Variable
{

    /**
     * Operation to be called with given parameters
     * @var Operation $operation
     * @var (Variable|Expression)[]
     */
    public Operation $operation;
    public array $parameters;

    /**
     * 
     * @param \Main\Interpreter\Operation $operation
     * @param array $parameters
     * @constructor
     */
    public function __construct(Operation $operation, array $parameters) {
        $this->operation = $operation;
        $this->parameters = $parameters;

        foreach ($parameters as $variable)
            $this->findDependencies($variable, $this->dependencies);

        $this->solve();
    }

    /**
     * Finds dependencies
     * @param type $variable
     * @param type $needed
     * @return void
     */
    private function findDependencies($variable, &$dependencies): void {
        foreach ($variable->dependencies as $var){
            if (in_array($var, $dependencies))
                continue;
            $dependencies[] = $var;
        }
    }

    public function solve(): bool {
        foreach ($this->dependencies as $var){
            if ($var instanceof Expression)
                $var->solve();
            if (!$var->resolved)
                return false;
        }

        $args = [];
        $this->value = $this->operation->execute(...$this->parameters);
        return true;
    }

}
