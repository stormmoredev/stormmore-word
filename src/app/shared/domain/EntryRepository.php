<?php

namespace app\shared\domain;

use infrastructure\Database;

readonly class EntryRepository
{
    public function __construct(
        private Database $database
    )
    {
    }

    public function getById(int $id): ?object
    {
        $query = "SELECT * FROM entries WHERE id = ?";
        return $this->database->fetchOne($query, $id);
    }

    public function getBySlug(string $slug): ?object
    {
        $query = "SELECT * FROM entries WHERE slug = ?";
        return $this->database->fetchOne($query, $slug);
    }

    public function updateRepliesCounterAndLastActivityTime(int $entryId): void
    {
        $query = "UPDATE entries  SET replies_num = replies_num + 1, last_activity_at = now() WHERE id = ?";
        $this->database->update($query, $entryId);
    }

    public function hasVoted(int $entryId, int $userId): bool
    {
        $query = "SELECT * FROM entry_votes WHERE entry_id = ? AND user_id = ?";
        $record = $this->database->fetchOne($query, $entryId, $userId);
        return !empty($record);
    }

    public function incrementVote(int $entryId): void
    {
        $query = "UPDATE entries  SET votes_num = entries.votes_num + 1, last_activity_at = now() WHERE id = ?";
        $this->database->update($query, $entryId);
    }

    public function insertVote(int $entryId, int $userId): void
    {
        $query = "INSERT INTO entry_votes (entry_id, user_id) VALUES (?, ?)";
        $this->database->insertWithoutGeneratedId($query, $entryId, $userId);
    }

    public function decrementVote(int $entryId): void
    {
        $query = "UPDATE entries  SET votes_num = entries.votes_num - 1, last_activity_at = now() WHERE id = ?";
        $this->database->update($query, $entryId);
    }

    public function deleteVote(int $entryId, int $userId): void
    {
        $query = "DELETE FROM entry_votes WHERE entry_id = ? AND user_id = ?";
        $this->database->insertWithoutGeneratedId($query, $entryId, $userId);
    }
}