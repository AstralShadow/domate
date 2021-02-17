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
use MathExam\ExerciseGroup as ExerciseGroup;

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
                if (!isset($this->data)){
                    $this->load();
                }
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
    public static function create(Database $database, User $owner): Test {
        $collection = $database->tests;

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

        $query = [
            '$push' => [
                "tests" => $id
            ]
        ];
        $owner->update($query);

        return new Test($database, $id);
    }

    /**
     * Appends exercise-group to the test. Random task will be selected from the group
     * @param TaskGroup $group
     * @param int $repeat
     * @return void
     */
    public function addExerciseGroup(ExerciseGroup $group, int $repeat = 1): void {
        $query = [
            '$push' => [
                "contents" => [
                    "id" => $group->getId(),
                    "token" => new ObjectId(),
                    "repeat" => min($repeat, 1)
                ]
            ]
        ];
        $this->update($query);
    }

    /**
     * Removes exercise-group to the test. Random task will be selected from the group
     * @param TaskGroup $group
     * @param int $repeat
     * @return void
     */
    public function removeExerciseGroup(ObjectId $token): void {
        $query = [
            '$pull' => [
                "contents" => [
                    "token" => $token
                ]
            ]
        ];
        $this->update($query);
    }

    public function getContentTokens(): array {
        $contents = (array) $this->contents ?? [];
        $groups = [];
        foreach ($contents as $pair){
            $groups[] = (string) $pair["token"];
        }
        return $groups;
    }

}
