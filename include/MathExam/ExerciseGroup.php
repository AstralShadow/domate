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

class ExerciseGroup
{

    use ModificableMongoDocument;

    public function __construct(Database $database, ObjectId $id) {
        $this->collection = $database->exerciseGroups;
        $this->privateParameters = ["owner"];
        $this->identificator = $id;
    }

    public function __get(string $key) {
        switch ($key){
            case "owner":
                return $this->data["owner"];
        }
        return $this->get($key);
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
    public static function create(Database $database, User $owner): ExerciseGroup {
        $collection = $database->exerciseGroups;

        $result = $collection->insertOne([
            "name" => "",
            "owner" => $owner->user,
            "description" => "",
            "contents" => [],
            "created" => new UTCDateTime(),
            "modified" => new UTCDateTime()
        ]);

        if (!$result->isAcknowledged()){
            return null;
        }
        $id = $result->getInsertedId();

        $owner->update([
            '$push' => [
                "exerciseGroups" => $id
            ]
        ]);

        return new ExerciseGroup($database, $id);
    }

    /**
     * Appends task
     * @param Task $task
     * @param int $repeat
     * @return void
     */
    public function addExercise(Exercise $exercise): void {
        $this->update([
            '$push' => [
                "contents" => $exercise->getId()
            ]
        ]);
    }

    /**
     * Removes task
     * @param Exercise $exercise
     * @return void
     */
    public function removeExercise(Exercise $exercise): void {
        $this->update([
            '$pull' => [
                "contents" => $exercise->getId()
            ]
        ]);
    }

    public function getContentTokens(): array {
        $contents = (array) $this->contents ?? [];
        $groups = [];
        foreach ($contents as $c_oid){
            $groups[] = (string) $c_oid;
        }
        return $groups;
    }

}
