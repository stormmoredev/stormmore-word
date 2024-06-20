<?php

namespace app\authentication;

use infrastructure\Database;
use stdClass;

class UserRepository
{
    function __construct (
        private Database $database
    ) { }

    function getById(int $id)
    {
        $query = 'SELECT id, name, email FROM users WHERE id = ?';
        return $this->database->fetchOne($query, $id);
    }
    function getByUsername(string $username)
    {
        $query = 'SELECT id, name, email FROM users WHERE name = ?';
        return $this->database->fetchOne($query, $username);
    }

    function getByEmail(string $email): ?stdClass
    {
        $query = 'SELECT id, name, role, photo, email FROM users WHERE email = ?';
        return $this->database->fetchOne($query, $email);
    }

    public function getByEmailAndPassword(string $email, string $password)
    {
        $query = 'SELECT id, name, role, photo, is_activated FROM users WHERE email = ? AND password = ?';
        return $this->database->fetchOne($query, $email, $password);
    }

    public function getByNameAndPassword(string $name, string $password)
    {
        $query = 'SELECT id, name, role, is_activated FROM users WHERE name = ? AND password = ?';
        return $this->database->fetchOne($query, $name, $password);
    }

    public function create($user): int
    {
        $query = "INSERT INTO users (name, role, email, password) VALUES (?, ?, ?, ?)";

        $this->database->insert($query,
            $user->name,
            $user->role,
            $user->email,
            $user->password);

        return $this->database->lastInsertedId();
    }

    public function activate(int $id): void
    {
        $query = "UPDATE users SET is_activated = true WHERE id = ?";
        $this->database->update($query, $id);
    }
}