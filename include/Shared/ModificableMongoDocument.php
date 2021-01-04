<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Shared;

use \MongoDB\Model\BSONDocument as BSONDocument;
use \MongoDB\Collection as Collection;
use \MongoDB\BSON\UTCDateTime as UTCDateTime;

/**
 * An object whose parameters can be modified.
 * The object is stored in mongodb.
 * The object will be auto-updated per given time interval.
 * 
 * @author azcraft
 */
trait ModificableMongoDocument
{

    /**
     * Object's collection.
     * @var Collection
     */
    private Collection $collection;

    /**
     * A key that is used to identify this object
     * @var string
     */
    private string $databaseIdentifier = "_id";

    /**
     * List of database parameters that are private
     * @var array
     */
    private array $privateParameters;

    /**
     * Value of the identifier. Can be defined with load()
     * @var type
     */
    private $identificator;

    /**
     * Data from MongoDB
     * @var BSONDocument
     */
    private BSONDocument $data;

    /**
     * Last update time in UNIX time standard
     * @var int
     */
    private int $lastUpdateTime;

    /**
     * Time before redownload
     * @var int
     */
    private int $autoupdateInterval = 3;

    /**
     * Loads object data.
     * Returns true on success or false if not existsing.
     * @param $identificator
     * @return bool
     */
    private function load($identificator = null): bool {
        $identificator ??= $this->identificator;

        if (!isset($identificator)){
            return false;
        }

        $filter = [$this->databaseIdentifier => $identificator];
        $document = $this->collection->findOne($filter);

        if (!isset($document)){
            return false;
        }

        $this->identificator = $identificator;
        $this->data = $document;
        $this->lastUpdateTime = time();

        return true;
    }

    /**
     * Returns value of key
     * @param string $key
     * @return mixed $value
     */
    public function get(string $key) {
        $now = time();
        $lastUpdate = $this->lastUpdateTime ?? 0;
        $updateDelay = $this->autoupdateInterval;

        if (!in_array("_id", $this->privateParameters)){
            $this->privateParameters[] = "_id";
        }

        if (in_array($key, $this->privateParameters)){
            return null;
        }

        if ($now - $lastUpdate > $updateDelay){
            $this->load();
        }

        if (!isset($this->data["_id"]) && !$this->load()){
            return null;
        }

        $value = $this->data ?? null;
        $path = explode('.', $key);
        for ($i = 0; $i < count($path); $i++){
            $value = $value[$path[$i]] ?? null;
        }

        return $value;
    }

    /**
     * Alias of get
     * @param string $key
     * @return mixed
     */
    public function __get(string $key) {
        return $this->get($key);
    }

    /**
     * Executes an updateOne query with given update directive.
     * @param $query
     * @return bool $success
     */
    public function update($query): bool {
        if (!isset($this->data["_id"]) && !$this->load()){
            return false;
        }

        if (!in_array("_id", $this->privateParameters)){
            $this->privateParameters[] = "_id";
        }

        foreach ($this->privateParameters as $private){
            foreach ($query as $key => $value){
                if (isset($value[$private])){
                    unset($query[$key][$private]);
                }
            }
        }

        if (!isset($query['$set'])){
            $query['$set'] = [];
        }
        $query['$set']["modified"] = new UTCDateTime();

        $filter = ["_id" => $this->data["_id"]];
        $updateResult = $this->collection->updateOne($filter, $query);

        if (!$updateResult->getModifiedCount()){
            $this->load();
        }

        return (bool) $updateResult->getMatchedCount();
    }

    /**
     * Removes key
     * Values "key" and "_id" are not accessible.
     * @param string $key
     * @return bool $success
     */
    public function remove(string $key): bool {
        if (!isset($this->data["_id"])){
            return false;
        }

        if (!in_array("_id", $this->privateParameters)){
            $this->privateParameters[] = "_id";
        }

        if (in_array($key, $this->privateParameters)){
            return false;
        }

        $filter = ["_id" => $this->data["_id"]];
        $update = [
            '$set' => [
                "modified" => new \MongoDB\BSON\UTCDateTime()
            ],
            '$unset' => [
                $key => true
            ]
        ];
        $updateResult = $this->collection->updateOne($filter, $update);

        if ($updateResult->getModifiedCount()){
            $this->load($this->sessionKey);
        }

        return (bool) $updateResult->getMatchedCount();
    }

    /**
     * Sets value of key
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, $value) {
        if (!isset($this->data["_id"])){
            return;
        }

        if (!in_array("_id", $this->privateParameters)){
            $this->privateParameters[] = "_id";
        }

        if (in_array($name, $this->privateParameters)){
            return;
        }

        if (!isset($value)){
            $this->remove($name);

            return;
        }

        $update = [
            '$set' => [$name => $value]
        ];
        $this->update($update);
    }

}
