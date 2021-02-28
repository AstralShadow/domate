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
use MathExam\Test as Test;

class ActiveTest
{

    use ModificableMongoDocument;

    private Database $database;

    /**
     * Creates new test.
     * @param Database $database
     * @param User $teacher
     * @return Test
     */
    public static function create(Database $database, User $teacher): ActiveTest {
        $collection = $database->activeTests;
        $characters = array_merge(range('a', 'z'), range('0', '9'));
        $charactersLength = count($characters) - 1;

        do{
            $key = "";
            for ($i = 0; $i < 5; $i++){
                $key .= $characters[mt_rand(0, $charactersLength)];
            }
        } while (ActiveTest::getIdFromKey($database, $key) != null);

        $result = $collection->insertOne([
            "key" => $key,
            "teacher" => $teacher->user,
            "solutions" => [],
            "created" => new UTCDateTime(),
            "modified" => new UTCDateTime()
        ]);

        if (!$result->isAcknowledged()){
            return null;
        }
        $id = $result->getInsertedId();

        $query = [
            '$push' => [
                "activeTests" => $id
            ]
        ];
        $teacher->update($query);

        return new ActiveTest($database, $id);
    }

    public function __construct(Database $database, ObjectId $id) {
        $this->database = $database;
        $this->collection = $database->activeTests;
        $this->privateParameters = ["teacher"];
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

    public function getKey(): string {
        return $this->key;
    }

    public function getLink(): string {
        return "http://domate.sytes.net/?test=" . $this->getKey();
    }

    public static function getIdFromKey(Database $database, string $key): ?string {
        $collection = $database->activeTests;
        $filter = [
            "key" => $key
        ];
        $document = $collection->findOne($filter);
        return $document ? (string) $document["_id"] : null;
    }

    public static function exists(Database $database, ObjectId $id): bool {
        $collection = $database->activeTests;
        $filter = ["_id" => $id];
        $document = $collection->findOne($filter);
        return (bool) $document;
    }

}
