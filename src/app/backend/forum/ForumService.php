<?php

namespace app\backend\forum;

use infrastructure\Slug;

readonly class ForumService
{
    public function __construct (
        private ForumCategoryRepository     $forumCategoryRepository
    ) { }

    public function addCategory($name, $order, $description, $parent_id = null): void
    {
        $parent_id = empty($parent_id) ? null : $parent_id;
        $slug = Slug::slugify($name);
        $this->forumCategoryRepository->addCategory($name, $order, $slug, $description, $parent_id);
    }

    public function updateCategory($id, $name, $sequence, $description, $parent_id = null): void
    {
        $parent_id = empty($parent_id) ? null : $parent_id;
        $this->forumCategoryRepository->updateCategory($id, $name, $sequence, $description, $parent_id);
    }

    public function deleteCategory($id): void
    {
        $this->forumCategoryRepository->deleteCategory($id);
    }
}