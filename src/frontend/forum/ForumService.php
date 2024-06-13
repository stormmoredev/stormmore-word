<?php

namespace frontend\forum;

use authentication\StormUser;
use infrastructure\settings\Settings;

readonly class ForumService
{
    public function __construct(
        private StormUser       $stormUser,
        private Settings        $settings,
        private ForumRepository $forumRepository)
    { }

    public function addThread(string $title, string $content): int
    {
        $lang = $this->settings->defaultLanguage->primary;
        return $this->forumRepository->addThread($title, $content, $lang, $this->stormUser->getId());
    }

    public function addPost(int $threadId, string $content): int
    {
        $this->forumRepository->incrementRepliesCounterAndRefreshIssueDate($threadId);
        return $this->forumRepository->addPost($threadId, $this->stormUser->getId(), $content);
    }
}