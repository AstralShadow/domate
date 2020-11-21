<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main;

/**
 * User object.
 *
 * Usage:
 * {User}->create([string $name, string $pwd, ...]) : bool $success
 * {User}->athorize([string $name, string $pwd]) : bool $success
 * {User}->changePassword(string $oldPwd, string $newPwd) : bool $success
 * {User}->get(string $key) : mixed $value
 * {User}->set(string $key, mixed $value) : bool $success
 * Note:
 *  values pwd and _id are not accessible and name is permanent
 *
 * @author azcraft
 */
class User
{

    private ?string $username = null;
    private \MongoDB\Model\BSONDocument $data;
    private \DateTime $lastUpdateTime;
    private $lastError = USER_ERRCODE_NO_ERROR;
    private static $regex = [
        "user" => '/^[a-zA-Z0-9\_\-]{3,16}$/',
        "pwd" => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z\d]).{8,32}$/'
    ];

    public function __construct() {
        global $session;
        $username = $session->get("user");
        if (!isset($username))
            return;

        if (!$this->loadUser($username)){
            $session->set("user", null);
        }
    }

    /**
     * Checks if user exists
     * @global \MongoDB\Client $db
     * @param string $username
     * @return bool
     */
    public function userExists(string $username): bool {
        global $db;
        $users = $db->users;
        $document = $users->findOne(["name" => $username]);
        return isset($document);
    }

    /**
     * Loads user
     * @global \MongoDB\Client $db
     * @global \Main\Session $session
     * @param string $username
     * @return bool
     */
    private function loadUser(string $username): bool {
        global $db, $session;
        $users = $db->users;
        $document = $users->findOne(["name" => $username]);
        if (!isset($document)){
            $this->lastError = USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }

        $this->username = $document["name"];
        $this->data = $document;
        $this->lastUpdateTime = new \DateTime();
        $session->set("user", $username);
        return true;
    }

    /**
     * Returns the value for given key.
     * Values "pwd" and "_id" are not accessible.
     * @param string $key
     * @return mixed $value
     */
    public function get(string $key) {
        if (!isset($this->username)){
            $this->lastError = USER_ERRCODE_USER_DOES_NOT_EXIST;
            return null;
        }

        $now = intval((new \DateTime())->format('s'));
        if (intval($this->lastUpdateTime->format('s')) > $now - 3)
            $this->loadUser($this->data["name"]);

        if (in_array($key, ["pwd", "_id"]))
            return null;

        if (isset($this->data[$key]))
            return $this->data[$key];

        return null;
    }

    /**
     * Sets the value for given key.
     * Removes value if set to null.
     * Values "pwd" and "_id" are not accessible.
     * @global \MongoDB\Client $db
     * @param string $key
     * @param mixed $value
     * @return bool $success
     */
    public function set(string $key, $value): bool {
        global $db;
        $users = $db->users;

        if (!isset($this->username)){
            $this->lastError = USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }

        if (!isset($value))
            return $this->remove($key);

        if (in_array($key, ["name", "pwd", "_id"]))
            return false;

        $filter = ["_id" => $this->data["_id"]];
        $update = [
            '$set' => [
                $key => $value,
                "modified" => new \MongoDB\BSON\UTCDateTime()
            ]
        ];
        $updateResult = $users->updateOne($filter, $update);
        if (!$updateResult->getMatchedCount()){
            $this->lastError = USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }
        if (!$updateResult->getModifiedCount())
            $this->loadUser($this->userID);
        return true;
    }

    /**
     * Removes the given key.
     * Removes value if set to null.
     * Values "pwd" and "_id" are not accessible.
     * @global \MongoDB\Client $db
     * @param string $key
     * @return bool $success
     */
    public function remove(string $key): bool {
        global $db;
        $users = $db->users;

        if (!isset($this->username)){
            $this->lastError = USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }

        if (in_array($key, ["name", "pwd", "_id"]))
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
        $updateResult = $users->updateOne($filter, $update);
        if (!$updateResult->getMatchedCount()){
            $this->lastError = USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }
        if (!$updateResult->getModifiedCount())
            $this->loadUser($this->userID);
        return true;
    }

    public function __get(string $name) {
        return $this->get($name);
    }

    public function __set(string $name, $value) {
        $this->set($name, $value);
    }

    /**
     * Creates new user.
     * $data must contain keys "name" and "pwd".
     * Any extra values will be recorded in the user's data.
     * Keys "created" and "modified" will be added.
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool {
        global $db;
        $users = $db->users;

        $this->lastError = USER_ERRCODE_NO_ERROR;
        $errCodes = ["user" => USER_ERRCODE_ILLEGAL_USERNAME, "pwd" => USER_ERRCODE_ILLEGAL_PASSWORD];
        foreach ($this->regex as $key => $exp){
            if (!isset($data[$key]) || !is_string($data[$key]) || !preg_match($exp, $data[$key]))
                $this->lastError = $errCodes[$key];
        }
        if ($this->userExists($data["name"]))
            $this->lastError = USER_ERRCODE_USER_ALREADY_EXIST;
        if ($this->lastError !== USER_ERRCODE_NO_ERROR)
            return false;
        $data["pwd"] = password_hash($data["pwd"], PASSWORD_BCRYPT);
        $data["created"] = isset($data["created"]) ? $data["created"] : new \MongoDB\BSON\UTCDateTime();
        $data["modified"] = isset($data["modified"]) ? $data["modified"] : new \MongoDB\BSON\UTCDateTime();

        $users->insertOne($data);
        return true;
    }

    /**
     * Authorizes a user.
     * @param array $data
     * @return bool
     */
    public function authorize(array $data): bool {
        $this->lastError = USER_ERRCODE_NO_ERROR;
        $errCodes = ["user" => USER_ERRCODE_ILLEGAL_USERNAME, "pwd" => USER_ERRCODE_ILLEGAL_PASSWORD];
        foreach ($this->regex as $key => $exp){
            if (!isset($data[$key]) || !is_string($data[$key]) || !preg_match($exp, $data[$key]))
                $this->lastError = $errCodes[$key];
        }
        if ($this->lastError !== USER_ERRCODE_NO_ERROR)
            return false;
        if (!$this->userExists($data["name"]))
            $this->lastError = USER_ERRCODE_USER_DOES_NOT_EXIST;

        if (!$this->comparePassword($data["name"], $data["pwd"])){
            $this->lastError = USER_ERRCODE_WRONG_PASSWORD;
            return false;
        }

        return $this->loadUser($data["name"]);
    }

    /**
     * Changes password.
     * @global \MongoDB\Client $db
     * @param string $oldPwd
     * @param string $newPwd
     * @return bool
     */
    public function changePassword(string $oldPwd, string $newPwd): bool {
        global $db;
        $users = $db->users;

        if (!isset($this->username)){
            $this->lastError = USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }

        if (!preg_match($this->regex["pwd"], $newPwd)){
            $this->lastError = USER_ERRCODE_ILLEGAL_PASSWORD;
            return false;
        }
        if (!$this->comparePassword($this->username, $oldPwd))
            return false;


        $filter = ["_id" => $this->data["_id"]];
        $update = [
            '$set' => [
                "pwd" => password_hash($newPwd, PASSWORD_BCRYPT),
                "modified" => new \MongoDB\BSON\UTCDateTime()
            ]
        ];
        $updateResult = $users->updateOne($filter, $update);
        if (!$updateResult->getModifiedCount())
            $this->loadUser($this->userID);
        return true;
    }

    /**
     * Checks if username and password combination is correct.
     * @global \MongoDB\Client $db
     * @param string $name
     * @param string $pwd
     * @return bool
     */
    private static function comparePassword(string $name, string $pwd): bool {
        global $db;
        $users = $db->users;
        $document = $users->findOne(["name" => $name]);
        if (!isset($document))
            return false;
        return password_verify($pwd, $document["pwd"]);
    }

    /**
     * Returns last USER_ERRCODE_* and clears the memory
     * @return type
     */
    public function getLastErrorCode() {
        $error = $this->lastError;
        $this->lastError = USER_ERRCODE_NO_ERROR;
        return $error;
    }

}
