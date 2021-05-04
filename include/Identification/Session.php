<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Identification;

use \MongoDB\Database as Database;
use \MongoDB\BSON\UTCDateTime as UTCDateTime;
use Shared\ModificableMongoDocument as ModificableMongoDocument;

/**
 * Handles user session, uses a cookie
 *
 * Usage:
 * {Session}->get(string $key) : mixed $value
 * {Session}->set(string $key, mixed $value) : bool $success
 * Note:
 *  values key and _id are not accessible
 *
 * @author azcraft
 */
class Session
{

    use ModificableMongoDocument;

    /**
     * Loads or creates a session.
     */
    public function __construct(Database $database) {
        $this->collection = $database->sessions;
        $this->privateParameters = ["key"];
        $this->databaseIdentifier = "key";

        $newSession = false;
        if (!isset($_COOKIE[SESSION_COOKIE]) || !is_string($_COOKIE[SESSION_COOKIE])){
            $newSession = true;
        } else if (!preg_match("/^[a-zA-Z0-9]{23}$/", $_COOKIE[SESSION_COOKIE])){
            $newSession = true;
        } else if (!$this->load($_COOKIE[SESSION_COOKIE])){
            $newSession = true;
        }

        if ($newSession){
            $this->newSession();
        }
    }

    /**
     * Creates new cookie, starting new session.
     * @global \Main\type $db
     * @return void
     */
    private function newSession(): void {
        $sessions = $this->collection;
        $characters = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        $charactersLength = count($characters) - 1;

        do{
            $key = "";
            for ($i = 0; $i < 23; $i++){
                $key .= $characters[mt_rand(0, $charactersLength)];
            }
        } while ($this->sessionExists($key));

        $sessions->insertOne([
            "key" => $key,
            "created" => new \MongoDB\BSON\UTCDateTime(),
            "modified" => new \MongoDB\BSON\UTCDateTime()
        ]);

        setcookie(SESSION_COOKIE, $key, 0, "", "", false, true);
        $this->identificator = $key;
    }

    /**
     * Checks if session exists
     * @global type $db
     * @param string $key
     * @return bool
     */
    private function sessionExists(string $key): bool {
        $sessions = $this->collection;
        $document = $sessions->findOne(["key" => $key]);
        return isset($document);
    }

    /**
     * Clears the session.
     * @return void
     */
    public function clear(): void {
        $sessions = $this->collection;
        if (!isset($this->data)){
            return;
        }
        $filter = ["_id" => $this->data["_id"]];
        $cleanState = [
            "_id" => $this->data["_id"],
            "key" => $this->data["key"],
            "created" => $this->data["created"],
            "modified" => new UTCDateTime()
        ];
        $sessions->replaceOne($filter, $cleanState);
    }

    /**
     * Clears the session and resets the creation time.
     * @return void
     */
    public function reset(): void {
        $sessions = $this->collection;
        if (!isset($this->data)){
            return;
        }
        $filter = ["_id" => $this->data["_id"]];
        $cleanState = [
            "_id" => $this->data["_id"],
            "key" => $this->data["key"],
            "created" => new UTCDateTime(),
            "modified" => new UTCDateTime()
        ];
        $sessions->replaceOne($filter, $cleanState);
    }

}
