<?php

namespace infrastructure;

use DateTime;
use PDO;

readonly class Database
{
    public function __construct(
        private PDO $connection
    ) { }

    public function begin(): void
    {
         $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollback();
        }
    }

    public function insert(string $query, ...$args): string
    {
        $args = $this->prepareData($args);
        $stmt = $this->connection->prepare($query);
        $stmt->execute($args);
        return $this->connection->lastInsertId();
    }

    public function insertUuid(string $query, ...$args): string
    {
        $args = $this->prepareData($args);
        $stmt = $this->connection->prepare($query);
        $stmt->execute($args);
        return $stmt->fetchColumn();
    }

    public function update(string $query, ...$args): void
    {
        $args = $this->prepareData($args);
        $stmt = $this->connection->prepare($query);
        $stmt->execute($args);
    }

    public function delete(string $query, ...$args): void
    {
        $args = $this->prepareData($args);
        $stmt = $this->connection->prepare($query);
        $stmt->execute($args);
    }

    public function fetch( string $statement, ...$args): array
    {
        return $this->query($statement, null, $args);
    }

    public function fetchOne(string $statement, ...$args): object|null
    {
        $results = $this->query($statement, null, $args);
        return count($results) ? $results[0] : null;
    }

    public function lastInsertedId(): string
    {
        return $this->connection->lastInsertId();
    }

    private function prepareData($args): array
    {
        foreach($args as $key => $arg)
        {
            if ($arg instanceof DateTime)
            {
                $args[$key] = $arg->format('Y-m-d H:i:s T');
            }
            if (is_bool($arg))
            {
                $args[$key] = $arg ? 1 : 0;
            }
        }

        return $args;
    }

    private function query(string $query, ?string $obj, array $args): array
    {
        $args = $this->prepareData($args);
        $stmt = $this->connection->prepare($query);
        $stmt->execute($args);
        $result = [];
        while ($row = $stmt->fetchObject($obj)) {
            $result[] = $row;
        }

        return $result;
    }
}