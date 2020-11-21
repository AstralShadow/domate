<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main;

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
class Session {

    /**
     * @var string       $sessionKey      Unique session key, the cookie value.
     * @var BSONDocument $data            All session data. (Reloads if older than 3s)
     * @var DateTime     $lastUpdateTime  Timestamp of $data.
     */
    private string $sessionKey;
    private \MongoDB\Model\BSONDocument $data;
    private \DateTime $lastUpdateTime;

    /**
     * Loads or creates a session.
     */
    public function __construct() {
        $newSession = false;
        if (!isset($_COOKIE[SESSION_COOKIE]) || !is_string($_COOKIE[SESSION_COOKIE]))
            $newSession = true;
        else if (!preg_match("/^[a-zA-Z0-9]{23}$/", $_COOKIE[SESSION_COOKIE]))
            $newSession = true;
        else if (!$this->loadSession($_COOKIE[SESSION_COOKIE]))
            $newSession = true;

        if ($newSession)
            $this->newSession();
    }

    /**
     * Creates new cookie, starting new session.
     * @global \Main\type $db
     * @return void
     */
    private function newSession(): void {
        global $db;
        $sessions = $db->sessions;
        $characters = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        $charactersLength = count($characters) - 1;

        do {
            $key = "";
            for ($i = 0; $i < 23; $i++)
                $key .= $characters[mt_rand(0, $charactersLength)];
        } while ($this->sessionExists($key));

        $sessions->insertOne([
            "key" => $key,
            "created" => new \MongoDB\BSON\UTCDateTime(),
            "modified" => new \MongoDB\BSON\UTCDateTime()
        ]);

        setcookie(SESSION_COOKIE, $key, 0, "", "", false, true);
        $this->sessionKey = $key;
    }

    /**
     * Checks if session exists
     * @global type $db
     * @param string $key
     * @return bool
     */
    private function sessionExists(string $key): bool {
        global $db;
        $sessions = $db->sessions;
        $document = $sessions->findOne(["key" => $key]);
        return isset($document);
    }

    /**
     * Loads session.
     * Returns true on success or false if this session doesn't exists.
     * @global \Main\type $db
     * @param string $key
     * @return bool
     */
    private function loadSession(string $key): bool {
        global $db;
        $sessions = $db->sessions;
        $document = $sessions->findOne(["key" => $key]);
        if (!isset($document))
            return false;
        $this->sessionKey = $key;
        $this->data = $document;
        $this->lastUpdateTime = new \DateTime();
        return true;
    }

    /**
     * Returns the value for given key for current session.
     * Values "key" and "_id" are not accessible.
     * @param string $key
     * @return mixed $value
     */
    public function get(string $key) {
        $now = intval((new \DateTime())->format('s'));
        if (intval($this->lastUpdateTime->format('s')) > $now - 3)
            $this->loadSession($this->sessionKey);

        if (in_array($key, ["key", "_id"]))
            return null;

        if (isset($this->data[$key]))
            return $this->data[$key];

        return null;
    }

    /**
     * Sets the value for given key for current session.
     * Removes value if set to null.
     * Values "key" and "_id" are not accessible.
     * @global $db
     * @param string $key
     * @param mixed $value
     * @return bool $success
     */
    public function set(string $key, $value): bool {
        global $db;
        $sessions = $db->sessions;

        if (!isset($value))
            return $this->remove($key);

        if (in_array($key, ["key", "_id"]))
            return false;

        $filter = ["_id" => $this->data["_id"]];
        $update = [
            '$set' => [
                $key => $value,
                "modified" => new \MongoDB\BSON\UTCDateTime()
            ]
        ];
        $updateResult = $sessions->updateOne($filter, $update);
        if (!$updateResult->getMatchedCount())
            return false;
        if (!$updateResult->getModifiedCount())
            $this->loadSession($this->sessionKey);
        return true;
    }

    /**
     * Removes the given key for current session.
     * Removes value if set to null.
     * Values "key" and "_id" are not accessible.
     * @global $db
     * @param string $key
     * @return bool $success
     */
    public function remove(string $key): bool {
        global $db;
        $sessions = $db->sessions;

        if (in_array($key, ["key", "_id"]))
            return false;

        $filter = ["_id" => $this->data["_id"]];
        $update = [
            '$set' => [
                "modified" => new \MongoDB\BSON\UTCDateTime()
            ],
            '$unset' => [
                $key => true
            ]
        ];
        $updateResult = $sessions->updateOne($filter, $update);
        if (!$updateResult->getMatchedCount())
            return false;
        if (!$updateResult->getModifiedCount())
            $this->loadSession($this->sessionKey);
        return true;
    }

    public function __get(string $name) {
        return $this->get($name);
    }

    public function __set(string $name, $value) {
        $this->set($name, $value);
    }

    // TODO: plan a remove/clear/reset session function
}
