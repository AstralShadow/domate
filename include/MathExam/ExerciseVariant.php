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
use Shared\ModificableMongoDocument as ModificableMongoDocument;
use MathExam\Exercise as Exercise;
use MathExam\TestSolution as TestSolution;

class ExerciseVariant
{

    private Database $database;

    use ModificableMongoDocument {
        dump as private _dump;
    }

    public function __construct(Database $database, ObjectId $id) {
        $this->collection = $database->exerciseVariants;
        $this->database = $database;
        $this->identificator = $id;
    }

    public function getId() {
        return $this->identificator;
    }

    /**
     * Creates new test.
     * @param Database $database
     * @param User $owner
     * @return Test
     */
    public static function create(Database $database, Exercise $origin, TestSolution $paper): ExerciseVariant {
        $collection = $database->exerciseVariants;

        $document = [
            "name" => $origin->name,
            "description" => $origin->description,
            "question" => $origin->question,
            "paper" => $paper->getId(),
            "answer" => null,
            "answerTime" => null,
            "correctMarker" => null,
            "checked" => false,
            "created" => new UTCDateTime(),
            "modified" => new UTCDateTime()
        ];
        if ($origin->useAnswer){
            $document["correctAnswer"] = $origin->answer;
        }
        $result = $collection->insertOne($document);

        if (!$result->isAcknowledged()){
            return null;
        }
        $id = $result->getInsertedId();

        return new ExerciseVariant($database, $id);
    }

    public function setAnswer(string $answer): void {
        $paper = new TestSolution($this->database, $this->paper);
        if ($paper->finished->toDateTime()->getTimeStamp() < time()){
            return;
        }
        $query = [
            '$set' => [
                "answer" => $answer,
                "answerTime" => new UTCDateTime(),
                "checked" => false
            ]
        ];
        $this->update($query);
    }

    public static function exists(Database $database, ObjectId $id): bool {
        $collection = $database->exerciseVariants;
        $filter = ["_id" => $id];
        $document = $collection->findOne($filter);
        return (bool) $document;
    }

    public function dump() {
        $data = $this->_dump();
        $private = [
            "name",
            "description",
            "paper",
            "correctCheck",
            "changedSinceCheck",
            "correctAnswer"
        ];
        foreach ($private as $param){
            if (isset($data[$param])){
                unset($data[$param]);
            }
        }
        return $data;
    }

}
