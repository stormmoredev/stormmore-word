<?php

namespace app\frontend\forum;

use app\authentication\StormUser;
use app\shared\SlugBuilder;
use infrastructure\settings\Settings;

readonly class ForumService
{
    public function __construct(
        private StormUser       $stormUser,
        private Settings        $settings,
        private SlugBuilder     $slugBuilder,
        private ForumRepository $forumRepository)
    { }

    public function addThread(int $cid, string $title, string $content): string
    {
        $slug = $this->slugBuilder->buildUniqueEntrySlug($title);
        $id = $this->forumRepository->addThread($cid, $title, $slug, $content, $this->stormUser->getId());
        return $slug;
    }

    public function addPost(int $threadId, string $content): int
    {
        $this->forumRepository->updateRepliesCounterAndLastActivityTime($threadId);
        return $this->forumRepository->addPost($threadId, $this->stormUser->getId(), $content);
    }
}