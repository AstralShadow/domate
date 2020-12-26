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

    private \MongoDB\Database $database;
    private \Main\Session $session;
    private ?string $username = null;
    private \MongoDB\Model\BSONDocument $data;
    private \DateTime $lastUpdateTime;

    private const regex = [
        "user" => '/^[a-zA-Z0-9\_\-]{3,16}$/',
        "pwd" => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z\d]).{8,32}$/'
    ];

    private function __construct(\MongoDB\Database $database, \Main\Session $session) {
        $this->session = $session;
        $this->database = $database;
    }

    public static function fromSession(\MongoDB\Database $database, \Main\Session $session, int &$errorByte = 0) {
        $username = $session->user;
        if (!isset($username))
            return;

        $object = new self($database, $session);
        if ($object->loadUser($username, $errorByte))
            return $object;

        $session->user = null;
    }

    /**
     * Authorizes a user.
     * @param array $data
     * @return bool
     */
    public static function authorize(\MongoDB\Database $database, \Main\Session $session, array $data, int &$errorByte = 0) {
        $errCodes = ["user" => USER_ERRCODE_WRONG_PASSWORD, "pwd" => USER_ERRCODE_WRONG_PASSWORD];
        foreach (self::regex as $key => $exp){
            if (!isset($data[$key]) || !is_string($data[$key]) || !preg_match($exp, $data[$key]))
                $errorByte = $errorByte | $errCodes[$key];
        }
        if ($errorByte !== USER_ERRCODE_NO_ERROR)
            return;
        if (!self::userExists($database, $data["user"]))
            $errorByte = $errorByte | USER_ERRCODE_USER_DOES_NOT_EXIST;

        if (!self::comparePassword($database, $data["user"], $data["pwd"])){
            $errorByte = $errorByte | USER_ERRCODE_WRONG_PASSWORD;
            return;
        }

        $object = new self($database, $session);
        if ($object->loadUser($data["user"], $errorByte))
            return $object;
    }

    /**
     * Checks if user exists
     * @global \MongoDB\Client $db
     * @param string $username
     * @return bool
     */
    public static function userExists(\MongoDB\Database $database, string $username): bool {
        $users = $database->users;
        $document = $users->findOne(["user" => $username]);
        return isset($document);
    }

    /**
     * Loads user
     * @global \MongoDB\Client $db
     * @global \Main\Session $session
     * @param string $username
     * @return bool
     */
    private function loadUser(string $username, int &$errorByte = 0): bool {
        $users = $this->database->users;
        $document = $users->findOne(["user" => $username]);
        if (!isset($document)){
            $errorByte = $errorByte | USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }

        $this->username = $document["user"];
        $this->data = $document;
        $this->lastUpdateTime = new \DateTime();
        $this->session->user = $username;
        return true;
    }

    /**
     * Returns the value for given key.
     * Values "pwd" and "_id" are not accessible.
     * @param string $key
     * @return mixed $value
     */
    public function get(string $key, int &$errorByte = 0) {
        if (!isset($this->username)){
            $errorByte = $errorByte | USER_ERRCODE_USER_DOES_NOT_EXIST;
            return null;
        }

        $now = intval((new \DateTime())->format('s'));
        if (intval($this->lastUpdateTime->format('s')) > $now - 3)
            $this->loadUser($this->data["user"], $errorByte);

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
    public function update($query, int &$errorByte = 0): bool {
        $users = $this->database->users;

        foreach (["user", "pwd", "_id"] as $private){
            foreach ($query as $key => $value)
                if (isset($value[$private]))
                    unset($query[$key][$private]);
        }

        if (!isset($query['$set']))
            $query['$set'] = [];
        $query['$set']["modified"] = new \MongoDB\BSON\UTCDateTime();

        $filter = ["_id" => $this->data["_id"]];
        $updateResult = $users->updateOne($filter, $query);

        if (!$updateResult->getMatchedCount()){
            $errorByte = $errorByte | USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }
        if (!$updateResult->getModifiedCount())
            $this->loadUser($this->userID, $errorByte);
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
    public function remove(string $key, int &$errorByte = 0): bool {
        $users = $this->database->users;

        if (in_array($key, ["user", "pwd", "_id"]))
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
            $errorByte = $errorByte | USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }
        if (!$updateResult->getModifiedCount())
            $this->loadUser($this->userID, $errorByte);
        return true;
    }

    public function __get(string $name) {
        return $this->get($name);
    }

    public function __set(string $name, $value) {
        if (in_array($name, ["key", "_id"]))
            return;

        if (!isset($value)){
            $this->remove($name);
            return;
        }

        $update = [
            '$set' => [
                $name => $value,
                "modified" => new \MongoDB\BSON\UTCDateTime()
            ]
        ];
        $this->update($update);
    }

    /**
     * Creates new user.
     * $data must contain keys "user" and "pwd".
     * Any extra values will be recorded in the user's data.
     * Keys "created" and "modified" will be added.
     * @param array $data
     * @return bool
     */
    public static function create(\MongoDB\Database $database, array $data, int &$errorByte = 0): bool {
        $users = $database->users;

        $errCodes = [
            "user" => USER_ERRCODE_ILLEGAL_USERNAME,
            "pwd" => USER_ERRCODE_ILLEGAL_PASSWORD
        ];
        foreach (self::regex as $key => $exp){
            if (!isset($data[$key]) || !is_string($data[$key]) || !preg_match($exp, $data[$key]))
                $errorByte = $errorByte | $errCodes[$key];
        }
        if (self::userExists($database, $data["user"]))
            $errorByte = $errorByte | USER_ERRCODE_USER_ALREADY_EXIST;
        if ($errorByte !== USER_ERRCODE_NO_ERROR)
            return false;
        $data["pwd"] = password_hash($data["pwd"], PASSWORD_BCRYPT);
        $data["created"] = isset($data["created"]) ? $data["created"] : new \MongoDB\BSON\UTCDateTime();
        $data["modified"] = isset($data["modified"]) ? $data["modified"] : new \MongoDB\BSON\UTCDateTime();

        $users->insertOne($data);
        return true;
    }

    /**
     * Changes password.
     * @global \MongoDB\Client $db
     * @param string $oldPwd
     * @param string $newPwd
     * @return bool
     */
    public function changePassword(string $oldPwd, string $newPwd, int &$errorByte = 0): bool {
        $users = $this->database->users;

        if (!isset($this->username)){
            $errorByte = $errorByte | USER_ERRCODE_USER_DOES_NOT_EXIST;
            return false;
        }

        if (!preg_match($this->regex["pwd"], $newPwd)){
            $errorByte = $errorByte | USER_ERRCODE_ILLEGAL_PASSWORD;
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
    private static function comparePassword(\MongoDB\Database $database, string $name, string $pwd): bool {
        $users = $database->users;
        $document = $users->findOne(["user" => $name]);
        if (!isset($document))
            return false;
        return password_verify($pwd, $document["pwd"]);
    }

    /**
     * Returns error messages from dictionary, each on new line.
     * @param type $dictionary
     * @param type $errorByte
     * @return string
     */
    public static function getErrorMessage(\Main\Dictionary $dictionary, int $errorByte): string {
        $possibleCodes = [
            USER_ERRCODE_NO_ERROR => "USER_ERRCODE_NO_ERROR",
            USER_ERRCODE_USER_ALREADY_EXIST => "USER_ERRCODE_USER_ALREADY_EXIST",
            USER_ERRCODE_USER_DOES_NOT_EXIST => "USER_ERRCODE_USER_DOES_NOT_EXIST",
            USER_ERRCODE_ILLEGAL_USERNAME => "USER_ERRCODE_ILLEGAL_USERNAME",
            USER_ERRCODE_ILLEGAL_PASSWORD => "USER_ERRCODE_ILLEGAL_PASSWORD",
            USER_ERRCODE_WRONG_PASSWORD => "USER_ERRCODE_WRONG_PASSWORD"
        ];
        $messages = $dictionary->user_class_error_messages;
        $result = "";
        foreach ($possibleCodes as $code => $name){
            if ($errorByte & $code)
                $result .= $messages[$name] . "\n";
        }
        return trim($result);
    }

}
