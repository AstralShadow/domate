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

class ExerciseVariant
{

    use ModificableMongoDocument;

    public function __construct(Database $database, ObjectId $id) {
        $this->collection = $database->exerciseVariants;
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
    public static function create(Database $database, Exercise $origin): ExerciseVariant {
        $collection = $database->exerciseVariants;

        $document = [
            "name" => $origin->name,
            "description" => $origin->description,
            "question" => $origin->question,
            "answer" => null,
            "answerTime" => null,
            "correctCheck" => null,
            "changedSinceCheck" => false,
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

}
