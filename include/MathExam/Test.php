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
use MathExam\ActiveTest as ActiveTest;

class Test
{

    use ModificableMongoDocument;

    private Database $database;

    public function __construct(Database $database, ObjectId $id) {
        $this->database = $database;
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
     * @param ?int $position
     * @return void
     */
    public function addExerciseGroup(ExerciseGroup $group, int $repeat = 1, ?int $position = null): void {
        $query = ['$push' => []];
        $item = [
            "id" => $group->getId(),
            "token" => new ObjectId(),
            "repeat" => min($repeat, 1)
        ];

        if (!isset($position)){
            $query['$push']["contents"] = $item;
        } else {
            $query['$push']["contents"] = [
                '$each' => [$item],
                '$position' => $position
            ];
        }
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

    public function moveExrciseGroup(ObjectId $token, int $position): void {
        $item = $this->getContent($token);
        if (!isset($item)){
            return;
        }
        $this->removeExerciseGroup($token);
        $query = ['$push' => []];
        $query['$push']["contents"] = [
            '$each' => [$item],
            '$position' => $position
        ];
        $this->update($query);
    }

    public function getContent(ObjectId $token): ?array {
        $contents = (array) $this->contents ?? [];
        foreach ($contents as $pair){
            if ($pair["token"] == $token){
                return (array) $pair;
            }
        }
        return null;
    }

    public function getContentTokens(): array {
        $contents = (array) $this->contents ?? [];
        $groups = [];
        foreach ($contents as $pair){
            $groups[] = (string) $pair["token"];
        }
        return $groups;
    }

    /**
     * Schedules a test.
     * @param User $teacher
     * @param int $start
     * @param int $end
     * @param int $worktime
     * @param string $note
     * @return string
     */
    public function schedule(User $teacher, int $start, int $end, int $worktime, string $question, ?string $note): ActiveTest {
        $active = ActiveTest::create($this->database, $teacher);
        $active->start = new UTCDateTime(max(time(), $start) * 1000);
        $active->end = new UTCDateTime(max(time() + 60, $end) * 1000);
        $active->worktime = max(1, $worktime);
        if (isset($note)){
            $active->note = $note;
        }
        $active->test = $this->getId();
        $active->question = $question;

        $query = [
            '$push' => [
                "active" => $active->getId()
            ]
        ];
        $this->update($query);

        return $active;
    }

    public static function exists(Database $database, ObjectId $id): bool {
        $collection = $database->tests;
        $filter = ["_id" => $id];
        $document = $collection->findOne($filter);
        return (bool) $document;
    }

}
