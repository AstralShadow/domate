<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MathExam;

class Dictionary implements \ArrayAccess
{

    private string $language;
    private $data;

    public function __construct($lang = "bg_BG") {
        if (file_exists("data/dictionaries/" . $lang . ".json")){
            $this->language = $lang;
            $text = file_get_contents("data/dictionaries/" . $lang . ".json");
            $this->data = json_decode($text, true);
        }
    }

    public function __get($name) {
        return $this->data[$name] ?? "";
    }

    public function offsetExists($offset): bool {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value): void {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset): void {
        unset($this->data[$offset]);
    }

}
