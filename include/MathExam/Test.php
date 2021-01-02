<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MathExam;

use \MongoDB\BSON as BSON;
use \MongoDB\Model as Model;

class Test
{

    /**
     * @var \MongoDB\BSON\ObjectId $id  this test's id
     * @var \MongoDB\BSON\ObjectId $owner  owner's id
     * @var \MongoDB\Model\BSONDocument $data  all data from database about this test
     */
    private BSON\ObjectId $id;
    private Model\BSONDocument $data;

    public function __construct() {
        
    }

    public function __get(string $name) {
        $keys = ["name"];
        if (in_array($name, $keys)){
            return $this->data[$name] ?? null;
        }
    }

    public function __set(string $name, $value) {
        $keys = ["name"];
        if (in_array($name, $keys)){
            $this->set($name, $value);
        }
    }

    /**
     * 
     * @param \MongoDB\Database $db
     * @param \MongoDB\BSON\ObjectId $id
     * @return \MathExam\Test
     */
    public static function load(Database $db, BSON\ObjectId $id): Test {
        
    }

    /**
     * 
     * @param \MongoDB\Database $db
     * @param \MongoDB\BSON\ObjectId $id
     * @return \MathExam\Test
     */
    public static function save(Database $db): Test {
        
    }

}
