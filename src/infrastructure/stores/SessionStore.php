<?php

use infrastructure\Database;

class SessionStore
{
    public function __construct(
        private Database $database
    ) { }

    public function create(int $userId, DateTime $validTo, bool $remember): string
    {
        $query = 'INSERT INTO sessions (user_id, valid_to, remember) VALUES (?, ?, ?) RETURNING id';

        return $this->database->insertUuid($query, $userId, $validTo, $remember);
    }

    public function load(string $id): ?stdClass
    {
        $query =
            'SELECT * FROM sessions 
            LEFT JOIN users u on sessions.user_id = u.id 
            WHERE sessions.id = ?';

        $session =  $this->database->fetchOne($query, $id);
        if ($session == null) return null;

        $session->valid_to = new DateTime($session->valid_to);

        return $session;
    }

    public function update(string $id, $validTo): void
    {
        $query = "UPDATE sessions SET valid_to = ?, last_activity_at = ? WHERE id = ?";
        $this->database->update($query, $validTo, new DateTime(), $id);
    }
}