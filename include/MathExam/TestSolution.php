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
use Shared\ModificableMongoDocument as ModificableMongoDocument;
use MathExam\Test as Test;
use MathExam\ActiveTest as ActiveTest;
use MathExam\TestVariantGenerator as TestVariantGenerator;
use MathExam\ExerciseVariant as ExerciseVariant;

class TestSolution
{

    use ModificableMongoDocument {
        dump as private _dump;
    }

    private Database $database;

    public function __construct(Database $database, ObjectId $id) {
        $this->database = $database;
        $this->collection = $database->testSolutions;
        $this->identificator = $id;
    }

    public function getId() {
        return $this->identificator;
    }

    public static function create(Database $database, ActiveTest $pack): TestSolution {
        $collection = $database->testSolutions;

        if (!Test::exists($database, new ObjectId($pack->test))){
            return null;
        }

        $endTime = time() + $pack->worktime * 60;
        $result = $collection->insertOne([
            "collection" => $pack->getId(),
            "origin" => $pack->test,
            "finished" => new UTCDateTime($endTime * 1000),
            "closed" => false,
            "identification" => null,
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
                "solutions" => $id
            ]
        ];
        $pack->update($query);

        $solution = new TestSolution($database, $id);
        $test = new Test($database, $pack->test);

        TestVariantGenerator::generateTestVariant($database, $solution, $test);
        return $solution;
    }

    public function close(): void {
        if ($this->closed){
            return;
        }
        $max = $this->created;
        foreach ($this->tasks as $t_id){
            $task = new ExerciseVariant($this->database, new ObjectId($t_id));
            $maxTime = $max->toDateTime()->getTimestamp();
            $answerTime = $task->answerTime;
            if (isset($answerTime)){
                $editTime = $answerTime->toDateTime()->getTimestamp();
                if ($editTime > $maxTime){
                    $max = $task->answerTime;
                }
            }
        }
        $update = [
            '$set' => [
                "closed" => true,
                "finished" => $max
            ]
        ];
        $this->update($update);
    }

    public function dump() {
        $data = $this->_dump();
        $private = [
            "collection",
            "origin"
        ];
        foreach ($private as $param){
            if (isset($data[$param])){
                unset($data[$param]);
            }
        }
        return $data;
    }

    public function getDataForTeacher() {
        if ($this->finished->toDateTime()->getTimestamp() < time()){
            $this->close();
        }
        $data = $this->_dump();
        $private = [
            "collection",
            "origin"
        ];
        foreach ($private as $param){
            if (isset($data[$param])){
                unset($data[$param]);
            }
        }
        foreach ($data["tasks"] as $key => $oid){
            $task = new ExerciseVariant($this->database, new ObjectId($oid));
            $data["tasks"][$key] = $task->getDataForTeacher();
        }
        return $data;
    }

    public static function exists(Database $database, ObjectId $id): bool {
        $collection = $database->testSolutions;
        $filter = ["_id" => $id];
        $document = $collection->findOne($filter);
        return (bool) $document;
    }

}
