<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MathExam;

use \MongoDB\BSON\ObjectId as ObjectId;
use \MongoDB\BSON\UTCDateTime as UTCDateTime;
use \MongoDB\Database as Database;
use Identification\User as User;
use \Shared\ModificableMongoDocument as ModificableMongoDocument;

class Test
{

    use ModificableMongoDocument;

    public function __construct(Database $database, ObjectId $id) {
        $this->collection = $database->tests;
        $this->privateParameters = ["owner"];
        $this->identificator = $id;
    }

    public function __get(string $key) {
        switch ($key){
            case "owner":
                return $this->data["owner"];

            case "_id":
                return $this->identificator;
        }
        return $this->get($key);
    }

    public static function create(Database $database, User $owner): Test {
        $collection = $database->tests;

        $result = $collection->insertOne([
            "owner" => $owner->user,
            "tasks" => [],
            "created" => new UTCDateTime(),
            "modified" => new UTCDateTime()
        ]);

        if (!$result->isAcknowledged()){
            return null;
        }
        $id = $result->getInsertedId();

        $query = [
            '$push' => [
                "tests" => $id
            ]
        ];
        $owner->update($query);

        return new Test($database, $id);
    }

}
