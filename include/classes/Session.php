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
 *
 * @author azcraft
 */
class Session {

    private string $sessionKey;
    private \MongoDB\Model\BSONDocument $sessionData;
    private \DateTime $lastUpdateTime;

    public function __construct() {
        $newSession = false;
        if (!isset($_COOKIE[SESSION_COOKIE]) || !is_string($_COOKIE[SESSION_COOKIE]))
            $newSession = true;
        else if (!preg_match("/^[a-zA-Z0-9]{23}$/", $_COOKIE[SESSION_COOKIE]))
            $newSession = true;
        else if (!$this->sessionExists($_COOKIE[SESSION_COOKIE]))
            $newSession = true;
        else if (!$this->loadSession($_COOKIE[SESSION_COOKIE]))
            $newSession = true;

        if ($newSession)
            $this->newSession();
    }

    /**
     * Creates new cookie, starting new session.
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
        $this->sessionId = $key;
    }

    private function sessionExists(string $key): bool {
        global $db;
        $sessions = $db->sessions;
        $document = $sessions->findOne(["key" => $key]);
        return isset($document);
    }

    private function loadSession(string $key): bool {
        global $db;
        $sessions = $db->sessions;
        $document = $sessions->findOne(["key" => $key]);
        if (!isset($document))
            return false;
        $this->sessionKey = $key;
        $this->sessionData = $document;
        $this->lastUpdateTime = new \DateTime();
        return true;
    }

    public function get(string $key) {
        $now = intval((new \DateTime())->format('s'));
        if (intval($this->lastUpdateTime->format('s')) > $now - 5)
            $this->loadSession($this->sessionKey);

        if (in_array($key, ["key", "_id"]))
            return null;

        if (isset($this->sessionData[$key]))
            return $this->sessionData[$key];

        return null;
    }

    public function set(string $key, $value): bool {
        global $db;
        $sessions = $db->sessions;
        $filter = ["_id" => $this->sessionData["_id"]];
        if (isset($value)) {
            $update = [
                '$set' => [
                    $key => $value,
                    "modified" => new \MongoDB\BSON\UTCDateTime()
                ]
            ];
        } else {
            $update = [
                '$set' => [
                    "modified" => new \MongoDB\BSON\UTCDateTime()
                ],
                '$unset' => [
                    $key => $value
                ]
            ];
        }
        $updateResult = $sessions->updateOne($filter, $update);
        if (!$updateResult->getMatchedCount())
            return false;
        if (!$updateResult->getModifiedCount())
            $this->loadSession($this->sessionKey);
        return true;
    }

    // TODO: plan a remove/clear/reset session function
}
