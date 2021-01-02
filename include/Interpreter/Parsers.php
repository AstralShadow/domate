<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter;

/**
 * Contains functions used while parsing code
 *
 * @author azcraft
 */
class Parsers
{

    /**
     * Parses strings in "" or '' to variables.
     * @param type $elements
     * @return bool $success
     */
    public static function defineStrings(array &$elements): void {
        $inString = null;
        foreach ($elements as $key => $el){
            if (!is_string($el) && !$inString)
                continue;
            if (!is_string($el))
                throw new ParseException
                    ("Trying to put an variable into string.");

            if (!$inString && in_array($el, ['"', "'"])){
                $var = new Variable();
                $var->value = "";
                $inString = $el;
                $elements[$key] = null;
            }else if ($inString === $el){
                $inString = false;
                $var->resolved = true;
                $elements[$key] = $var;
            }else if ($inString){
                $var->value .= $el;
                $elements[$key] = null;
            }
        }
        self::removeEmptyElements($elements);
        if ($inString)
            throw new ParseException
                ("Unclosed string.");
    }

    /**
     * Removes all elements after # element
     * @param array $elements
     * @return bool $success
     */
    static function removeComments(array &$elements): void {
        for ($i = 0; $i < count($elements); $i++){
            if ($elements[$i] === '#')
                array_splice($elements, $i);
        }
    }

    /**
     * Parses floats and integers as floats
     * @param array $elements
     * @return bool $success
     */
    static function defineNumbers(array &$elements): void {
        foreach ($elements as $key => $el){
            if (!is_string($el))
                continue;
            if (is_numeric($el)){
                $elements[$key] = new Variable();
                $elements[$key]->resolved = true;
                $elements[$key]->value = floatval($el);
            }
        }
    }

    /**
     * Defines any word as variable that requires itself.
     * These variables have names
     * @param array $elements
     * @return bool $success
     */
    static function defineVariables(array &$elements) {
        foreach ($elements as $key => $el){
            if (!is_string($el))
                continue;
            if (!preg_match("/^[a-zA-Z0-9\_]+$/", $el))
                continue;

            $elements[$key] = new Variable();
            $elements[$key]->resolved = false;
            $elements[$key]->name = $el;
            $elements[$key]->dependencies = [$elements[$key]];
        }
    }

    /**
     * Puts all elements surrounded by braces in array.
     * Removes the braces.
     * @param array $elements
     * @return bool $success
     */
    static function groupSubExpressions(array &$elements): void {
        $stack = [];
        for ($i = 0; $i < count($elements); $i++){
            if (in_array($elements[$i], ['(', '[']))
                $stack[] = $i;

            if (!in_array($elements[$i], [']', ')']))
                continue;

            if (!count($stack))
                throw new ParseException
                    ("You can't close subexpression that you didn't opened.");

            $start = array_splice($stack, -1)[0];
            $count = $i - $start;
            $elements[$start] = array_splice($elements, $start + 1, $count);
            $i -= $count;
            unset($elements[$start][$count - 1]);
        }
        if (count($stack))
            throw new ParseException
                ("You forgot to close subexpression.");
    }

    /**
     * Creates Expression for a^b operation
     * @param array $elements
     * @return bool $success
     */
    static function parseExponentiation(array &$elements): void {
        while (in_array("^", $elements)){
            $i = array_search("^", $elements);
            if (!isset($elements[$i + 1]) || !isset($elements[$i - 1]))
                throw new ParseException
                    ("Trying to perform operation without parameters.");

            $parameters = [$elements[$i - 1], $elements[$i + 1]];
            foreach ($parameters as $p)
                if (!($p instanceof Variable))
                    throw new ParseException
                        ("Trying to perform operation with non-variables.");

            $operation = new Operations\Exponentiation();
            $elements[$i] = new Expression($operation, $parameters);
            unset($elements[$i + 1], $elements[--$i]);
            self::removeEmptyElements($elements);
        }
    }

    static function parseMultiplicationLevelOperations(array &$elements): void {
        self::removeEmptyElements($elements);
        for ($i = 0; $i < count($elements); $i++){
            $e = $elements[$i];

            if (!is_string($e) || !in_array($e, ["*", "\\", "/", ":", "%"]))
                continue;

            $parameters = [$elements[$i - 1], $elements[$i + 1]];
            foreach ($parameters as $p)
                if (!($p instanceof Variable))
                    throw new ParseException
                        ("Trying to perform operation with non-variables.");

            if (in_array($e, [":", "/"]))
                $operation = new Operations\Division();
            if (in_array($e, ["\\", "%"]))
                $operation = new Operations\Modulo();
            if ($e === "*")
                $operation = new Operations\Multiplication();

            $elements[$i] = new Expression($operation, $parameters);
            unset($elements[$i + 1], $elements[--$i]);
            self::removeEmptyElements($elements);
        }
    }

    static function parseSumLevelOperations(array &$elements): void {
        self::removeEmptyElements($elements);
        self::parseFirstNegativeNumber($elements);

        for ($i = 1; $i < count($elements); $i++){
            $e = $elements[$i];

            if (!is_string($e) || !in_array($e, ["+", "-"]))
                continue;

            $parameters = [$elements[$i - 1], $elements[$i + 1]];
            foreach ($parameters as $p)
                if (!($p instanceof Variable))
                    throw new ParseException
                        ("Trying to perform operation with non-variables.");

            if ($e === "+")
                $operation = new Operations\Addition();
            if ($e === "-")
                $operation = new Operations\Subtraction();

            $elements[$i] = new Expression($operation, $parameters);
            unset($elements[$i + 1], $elements[--$i]);
            self::removeEmptyElements($elements);
        }
        self::removeEmptyElements($elements);
    }

    static function parseFirstNegativeNumber(array &$elements): void {
        if (!isset($elements[0]) || $elements[0] !== "-")
            return;
        if (!($elements[1] instanceof Variable))
            throw new ParseException
                ("Trying to subtract non-variable.");

        $operation = new Operations\Subtraction();

        $zeroVar = new Variable();
        $zeroVar->resolved = true;
        $zeroVar->value = 0;

        $elements[0] = new Expression($operation, [$zeroVar, $elements[1]]);
        unset($elements[1]);
        $elements = array_values($elements);
    }

    /**
     * Removes all nulls or empty strings.
     * Resets indexing.
     * @param type $elements
     * @return void
     */
    static function removeEmptyElements(array &$elements): void {
        foreach ($elements as $key => $value){
            if (!isset($value))
                unset($elements[$key]);
            else if (is_string($value) && !strlen($value))
                unset($elements[$key]);
        }
        $elements = array_values($elements);
    }

}
