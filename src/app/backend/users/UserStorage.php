<?php

namespace app\backend\users;

use infrastructure\Database;

readonly class UserStorage
{
    public function __construct(
        private Database $database
    ) { }

    public function insert(object $user): string
    {
        $query =
            "INSERT INTO users (name, first_name, last_name, role, email, password) 
            VALUES (?, ?, ?, ?, ?, ?)";

        $this->database->insert($query,
            $user->name,
            $user->first_name,
            $user->last_name,
            $user->role,
            $user->email,
            $user->password);

        return $this->database->lastInsertedId();
    }

    public function update(object $user): void
    {
        $query =
            "UPDATE users 
            SET first_name = ?, last_name = ?, role = ?
            WHERE id = ?";

        $this->database->update($query,
            $user->first_name,
            $user->last_name,
            $user->role,
            $user->id);
    }

    public function getByCredentials(string $username, string  $password)
    {
        $query = 'SELECT id, name FROM users WHERE name = ? AND password = ?';
        $user = $this->database->fetchOne($query, $username, $password);

        return $user;
    }

    function find($userId)
    {
        $query = 'SELECT * FROM users WHERE id = ?';
        return $this->database->fetchOne($query, $userId);
    }

    function exist($username): bool
    {
        $query = 'SELECT id FROM users WHERE name = ?';
        $user = $this->database->fetchOne($query, $username);

        return $user !== null;
    }
}