<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main\Interpreter;

/**
 * Interpretes a Task, can create multiple TaskVariant objects
 *
 * @author azcraft
 */
class Interpreter
{

    private $task;
    private $systemNamespace = [];
    private $namespace = [];

    /**
     * Loads the task's code into the memory.
     * @param Task $task
     */
    public function __construct(Task $task) {
        $this->task = $task;

        $lines = explode("\n", $this->task->code);
        foreach ($lines as $i => $line){
            $exp = $this->parse([$line]);
            if (!$exp->resolved)
                $exp->solve();
            if (!$exp->resolved){
                var_dump($exp->parameters[1]);
                throw new ParseException("Can not resolve line " . $i);
            }
        }
    }

    /**
     * Parses input, returns an expression or null
     * @param array $expression
     * @return \Main\Interpreter\Expression|null
     */
    private function parse(array $expression) {
        $self = $this;
        $operators = [
            "/([\"\'])/" => function(&$expressions){
                Parsers::defineStrings($expressions);
            },
            "/ /" => function(&$elements){
                Parsers::removeEmptyElements($elements);
            },
            "/(\#)/" => function(&$expressions){
                Parsers::removeComments($expressions);
            },
            "/([^a-zA-Z0-9\_])(\d+\.\d+)/" => function(&$expressions){
                Parsers::defineNumbers($expressions);
            },
            "/([^a-zA-Z0-9\_])(\d+)/" => function(&$expressions){
                Parsers::defineNumbers($expressions);
            },
            "/([a-zA-Z0-9\_]+)/" => function(&$expressions) use ($self){
                Parsers::defineVariables($expressions);
                foreach ($expressions as $key => $var){
                    if (!($var instanceof Variable) || !isset($var->name))
                        continue;
                    $variable = $self->getVariable($var->name);
                    if (!isset($variable))
                        continue;
                    $expressions[$key] = $variable;
                }
                // if varname is of type operation
            },
            "/([\\(\\)\\[\\]])/" => function(&$expressions) use ($self){
                Parsers::groupSubExpressions($expressions);
                foreach ($expressions as $key => $e){
                    if (!is_array($e))
                        continue;

                    $subExpression = $self->parse($e);
                    if (!isset($subExpression))
                        throw new ParseException("Uncaught exception.");
                    $expressions[$key] = $subExpression;
                }
            },
            // Insert method calls here, checking for
            //  variable next to variable or seperated with dot
            "/(\\^)/" => function(&$expressions){
                Parsers::parseExponentiation($expressions);
                // TODO: don't forget to add front-end error for -5^2, like in js:
                //  Unary operator used immediately before exponentiation expression.
                //  Parenthesis must be used to disambiguate operator precedence
            },
            "/([\\/\\\\\*\\:\\%])/" => function(&$expressions){
                Parsers::parseMultiplicationLevelOperations($expressions);
            },
            "/([-\\+])/" => function(&$expressions){
                Parsers::parseSumLevelOperations($expressions);
            },
            "/(,)/" => function(&$elements){
                while (in_array(",", $elements)){
                    $i = array_search(",", $elements);
                    if (!isset($elements[$i + 1]) || !isset($elements[$i - 1]))
                        throw new ParseException
                            ("Missing parameter in array. Maybe you left an extra comma?");

                    $a = $elements[$i - 1];
                    $b = $elements[$i + 1];
                    if (!is_array($a))
                        $a = [$a];
                    if (!is_array($b))
                        $b = [$b];
                    $parameters = array_merge($a, $b);
                    foreach ($parameters as $p)
                        if (!($p instanceof Variable))
                            throw new ParseException
                                ("Trying to perform operation with non-variables.");

                    $elements[$i] = $parameters;
                    unset($elements[$i + 1], $elements[--$i]);
                    self::removeEmptyElements($elements);
                }
                foreach ($elements as $key => $el){
                    if (!is_array($el))
                        continue;
                    $var = new Variable();
                    $var->resolved = true;
                    $var->value = $el;
                    $elements[$key] = $var;
                } // TODO: make sure all operations work with arrays.
            },
            "/(\\=)/" => function(&$elements) use ($self){
                for ($i = count($elements) - 1; $i >= 0; $i--){
                    if ($elements[$i] != "=")
                        continue;

                    $parameters = [$elements[$i - 1], $elements[$i + 1]];
                    foreach ($parameters as $p)
                        if (!($p instanceof Variable))
                            throw new ParseException
                                ("Trying to perform operation with non-variables.");
                    if (!isset($parameters[0]->name))
                        throw new ParseException
                            ("You can't define nameless variable.");
                    $parameters[0]->resolved = true;
                    // Think of reverse-operation for operations,
                    // where possible. This may allow calculation
                    // of things with = on both sides

                    $operation = new class($self) implements Operation {

                        private $interpreter;

                        public function __construct($interpreter) {
                            $this->interpreter = $interpreter;
                        }

                        public function execute(...$args) {
                            $this->interpreter->setVariable($args[0], $args[1]);
                            return $args[1]->value;
                        }
                    };

                    $elements[$i - 1] = new Expression($operation, $parameters);
                    unset($elements[$i], $elements[$i + 1]);
                }
                Parsers::removeEmptyElements($elements);
            }
        ];
        foreach ($operators as $delimeterRegex => $operation){
            $newExpression = [];
            foreach ($expression as $element){
                if ($element instanceof Variable){
                    $newExpression[] = [$element];
                    continue;
                }

                $f = PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY;
                $pieces = preg_split($delimeterRegex, $element, 0, $f);
                $newExpression[] = $pieces;
            }
            $expression = array_merge([], ...$newExpression);
            $operation($expression);
        }

        return count($expression) ? $expression[0] : null;
    }

    public function setVariable(Variable $destination, Variable $source) {
        $this->namespace[$destination->name] = $source;
    }

    public function getVariable(string $name) {
        $global = $this->namespace;
        if (isset($global[$name])){
            if (!$global[$name]->resolved && $global[$name] instanceof Expression)
                $global[$name]->solve();
            return $global[$name];
        }

        $system = $this->systemNamespace;
        if (isset($system[$name])){
            return $system[$name];
        }

        return null;
    }

    public function generateVariant(): \Main\Interpreter\TaskVariant {
        $variant = new \Main\Interpreter\TaskVariant($this->task);

        return $variant;
    }

}
