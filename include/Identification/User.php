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
use Identification\Session as Session;
use MathExam\Dictionary as Dictionary;

/**
 * Handles user account, can be created by authorization or by session.
 * Most functions have $errorByte, which you can read with User::getErrorMessage
 *
 * @author azcraft
 */
class User
{

    use ModificableMongoDocument {
        load as private _load;
    }

    /**
     * Current session.
     * @var Session $session
     */
    private Session $session;

    /**
     * Regular expressions for username and password
     */
    private const VALIDATION_EXPRESSIONS = [
        "user" => '/^[\x{0400}-\x{04FF}A-Z0-9\_\-\.]{3,16}$/iu',
        // "pwd" => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z\d]).{8,72}$/'
        "pwd" => '/^.{6,72}$/'
    ];

    /**
     * Private construct method
     * @param \MongoDB\Database $database
     * @param \Main\Session $session
     */
    private function __construct(Database $database, Session $session) {
        $this->session = $session;
        $this->collection = $database->users;
        $this->privateParameters = ["pwd", "user"];
        $this->databaseIdentifier = "user";
    }

    /**
     * Constructs user object form session, if possible.
     * @param \MongoDB\Database $database
     * @param \Main\Session $session
     * @param int $errorByte
     * @return \self
     */
    public static function fromSession(Database $database, Session $session, int &$errorByte = 0) {
        $username = $session->user;
        if (!isset($username)){
            return;
        }

        $object = new self($database, $session);
        if ($object->load($username, $errorByte)){
            return $object;
        }

        $session->user = null;
    }

    /**
     * Authorizes a user by name and password
     * @param array $data
     * @return bool
     */
    public static function authorize(Database $database, Session $session, array $data, int &$errorByte = 0) {
        $errCodes = [
            "user" => USER_ERRCODE_WRONG_PASSWORD,
            "pwd" => USER_ERRCODE_WRONG_PASSWORD
        ];

        foreach (self::VALIDATION_EXPRESSIONS as $key => $exp){
            $ok = true;

            if (!isset($data[$key])){
                $ok = false;
            } else if (!is_string($data[$key])){
                $ok = false;
            } else if (!preg_match($exp, $data[$key])){
                $ok = false;
            }

            if (!$ok){
                $errorByte = $errorByte | $errCodes[$key];
            }
        }

        if ($errorByte !== USER_ERRCODE_NO_ERROR){
            return;
        }

        if (!self::userExists($database, $data["user"])){
            $errorByte = $errorByte | USER_ERRCODE_USER_DOES_NOT_EXIST;
            return;
        }

        if (!self::comparePassword($database, $data["user"], $data["pwd"])){
            $errorByte = $errorByte | USER_ERRCODE_WRONG_PASSWORD;
            return;
        }

        $object = new self($database, $session);
        if ($object->load($data["user"], $errorByte)){
            return $object;
        }
    }

    /**
     * Disconnects account from session
     * @return void
     */
    public function logout(): void {
        $this->session->remove("user");
    }

    /**
     * Checks if user exists
     * @global \MongoDB\Client $database
     * @param string $username
     * @return bool
     */
    public static function userExists(Database $database, string $username): bool {
        $collection = $database->users;
        $document = $collection->findOne(["user" => $username]);
        return isset($document);
    }

    /**
     * Loads user
     * @global \MongoDB\Client $db
     * @global \Main\Session $session
     * @param string $username
     * @return bool
     */
    private function load($username = null, int &$errorByte = 0): bool {
        $success = $this->_load($username);

        if ($success){
            $this->session->user = $this->identificator;
        } else {
            $errorByte = $errorByte | USER_ERRCODE_USER_DOES_NOT_EXIST;
        }

        return $success;
    }

    public function __get(string $key) {
        if ($key === "user"){
            return $this->identificator;
        }

        return $this->get($key);
    }

    /**
     * Creates new user.
     * $data must contain keys "user" and "pwd".
     * Any extra values will be recorded in the user's data.
     * Keys "created" and "modified" will be added.
     * @param array $data
     * @return bool
     */
    public static function create(Database $database, array $data, int &$errorByte = 0): bool {
        $collection = $database->users;
        $errCodes = [
            "user" => USER_ERRCODE_ILLEGAL_USERNAME,
            "pwd" => USER_ERRCODE_ILLEGAL_PASSWORD
        ];

        foreach (self::VALIDATION_EXPRESSIONS as $key => $exp){
            $ok = true;

            if (!isset($data[$key])){
                $ok = false;
            } else if (!is_string($data[$key])){
                $ok = false;
            } else if (!preg_match($exp, $data[$key])){
                $ok = false;
            }

            if (!$ok){
                $errorByte = $errorByte | $errCodes[$key];
            }
        }

        if (self::userExists($database, $data["user"])){
            $errorByte = $errorByte | USER_ERRCODE_USER_ALREADY_EXIST;
        }

        if ($errorByte !== USER_ERRCODE_NO_ERROR){
            return false;
        }

        $data["pwd"] = password_hash($data["pwd"], PASSWORD_BCRYPT);
        $data["created"] = $data["created"] ?? new UTCDateTime();
        $data["modified"] = $data["modified"] ?? new UTCDateTime();

        $collection->insertOne($data);
        return true;
    }

    /**
     * Changes password.
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
        if (!$this->comparePassword($this->username, $oldPwd)){
            return false;
        }

        $filter = ["_id" => $this->data["_id"]];
        $update = [
            '$set' => [
                "pwd" => password_hash($newPwd, PASSWORD_BCRYPT),
                "modified" => new UTCDateTime()
            ]
        ];
        $updateResult = $users->updateOne($filter, $update);
        if (!$updateResult->getModifiedCount()){
            $this->load();
        }
        return true;
    }

    /**
     * Checks if username and password combination is correct.
     * @param string $name
     * @param string $pwd
     * @return bool
     */
    private static function comparePassword(Database $database, string $name, string $pwd): bool {
        $users = $database->users;
        $document = $users->findOne(["user" => $name]);
        if (!isset($document)){
            return false;
        }
        return password_verify($pwd, $document["pwd"]);
    }

    /**
     * Returns error messages from dictionary, each on new line.
     * @param type $dictionary
     * @param type $errorByte
     * @return string
     */
    public static function getErrorMessage(Dictionary $dictionary, int $errorByte): string {
        $possibleCodes = [
            USER_ERRCODE_NO_ERROR => "USER_ERRCODE_NO_ERROR",
            USER_ERRCODE_USER_ALREADY_EXIST => "USER_ERRCODE_USER_ALREADY_EXIST",
            USER_ERRCODE_USER_DOES_NOT_EXIST => "USER_ERRCODE_USER_DOES_NOT_EXIST",
            USER_ERRCODE_ILLEGAL_USERNAME => "USER_ERRCODE_ILLEGAL_USERNAME",
            USER_ERRCODE_ILLEGAL_PASSWORD => "USER_ERRCODE_ILLEGAL_PASSWORD",
            USER_ERRCODE_WRONG_PASSWORD => "USER_ERRCODE_WRONG_PASSWORD"
        ];
        $messages = $dictionary->userClassErrorMessage;
        $result = "";
        foreach ($possibleCodes as $code => $name){
            if ($errorByte & $code){
                $result .= $messages[$name] . "\n";
            }
        }
        return trim($result);
    }

}
