<?php

namespace app\authentication;

use DateTime;
use infrastructure\Database;
use stdClass;

class UserTokenRepository
{
    function __construct (
        private Database $database
    ) { }

    public function create(int $userId, DateTime $validTo): string
    {
        $query = "INSERT INTO users_tokens (user_id, valid_to) VALUES (?, ?) RETURNING key";

        return $this->database->insertUuid($query, $userId, $validTo);
    }

    function getByKey(string $key): ?stdClass
    {
        $query = 'SELECT * FROM users_tokens WHERE key = ?';
        $token = $this->database->fetchOne($query, $key);
        if ($token) {
            $token->valid_to = new DateTime($token->valid_to);
        }

        return $token;
    }

    function delete(string $key): void
    {
        $query = 'DELETE FROM users_tokens WHERE key = ?';
        $this->database->delete($query, $key);
    }
}