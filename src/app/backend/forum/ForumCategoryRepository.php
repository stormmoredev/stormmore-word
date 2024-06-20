<?php

namespace app\backend\forum;

use infrastructure\Database;

readonly class ForumCategoryRepository
{
    public function __construct(
        private Database $database
    ) { }

    public function addCategory($name, $order, $slug, $description, $parent_id = null): void
    {
        $query = "INSERT into categories (name, sequence, slug, description, type, parent_id) VALUES (?, ?, ?, ?, ?, ?)";
        $this->database->insert($query, $name, $order, $slug, $description, 2, $parent_id);
    }

    public function updateCategory($id, $name, $sequence, $description, $parent_id = null): void
    {
        $query =  "UPDATE categories SET name = ?, sequence = ?, description = ?, parent_id = ? WHERE id = ?";
        $this->database->update($query, $name, $sequence, $description, $parent_id, $id);
    }

    public function deleteCategory($id): void
    {
        $query =  "UPDATE categories SET is_deleted = true WHERE id = ?";
        $this->database->update($query, $id);
    }

    public function getById(int $id): object
    {
        $query = "SELECT * FROM categories WHERE id = ?";
        return $this->database->fetchOne($query, $id);
    }
}