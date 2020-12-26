<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Main;

/**
 * Handles user account, can be created by authorization or by session.
 * Most functions have $errorByte, which you can read with User::getErrorMessage
 *
 * @author azcraft
 */
class User
{

    /**
     * @var Database $database  Mongodb database object
     * @var Session $session  Current session.
     * @var string $username  Current user
     * @var BSONDocument $data  All data. (Reloads if older than 3s)
     * @var DateTime $lastUpdateTime  Timestamp of $data.
     */
    private \MongoDB\Database $database;
    private Session $session;
    private string $username;
    private \MongoDB\Model\BSONDocument $data;
    private \DateTime $lastUpdateTime;

    /**
     * Regular expressions for username and password
     */
    private const regex = [
        "user" => '/^[a-zA-Z0-9\_\-]{3,16}$/',
        "pwd" => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z\d]).{8,32}$/'
    ];

    /**
     * Private construct method
     * @param \MongoDB\Database $database
     * @param \Main\Session $session
     */
    private function __construct(\MongoDB\Database $database, \Main\Session $session) {
        $this->session = $session;
        $this->database = $database;
    }

    /**
     * Constructs user object form session, if possible.
     * @param \MongoDB\Database $database
     * @param \Main\Session $session
     * @param int $errorByte
     * @return \self
     */
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
     * Authorizes a user by name and password
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
     * Disconnects account from session
     * @return void
     */
    public function logout(): void {
        $session = $this->session;
        $session->remove("user");
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

        $path = explode('.', $key);
        $value = null;
        if (count($path) && isset($this->data[$path[0]]))
            $value = $this->data[$path[0]];
        for ($i = 1; $i < count($path); $i++)
            if (isset($value[$i]))
                $value = $value[$i];

        return $value;
    }

    /**
     * Executes updateOne with given update directive
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

    /**
     * Alias of get
     * @param string $name
     * @return type
     */
    public function __get(string $name) {
        return $this->get($name);
    }

    /**
     * Sets value with given name in the user object
     * @param string $name
     * @param mixed $value
     * @return void
     */
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
