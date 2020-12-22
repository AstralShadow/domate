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
interface Operation
{

    /**
     * Returns value. Throws OperationException
     * @param type $args
     * @return mixed
     */
    function execute(...$args);
}
