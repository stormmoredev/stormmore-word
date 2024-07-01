<?php

namespace app\shared;

use app\authentication\StormUser;
use infrastructure\AjaxResult;
use PHPMailer\PHPMailer\Exception;

readonly class EntryService
{
    public function __construct(
        private EntryRepository $entryRepository,
        private StormUser       $stormUser)
    {
    }

    public function vote(string $slug): AjaxResult
    {
        $entry = $this->entryRepository->getBySlug($slug);
        $userId = $this->stormUser->id;

        if ($this->entryRepository->hasVoted($entry->id, $userId))
            return new AjaxResult(0);

        $this->entryRepository->insertVote($entry->id, $userId);
        $this->entryRepository->incrementVote($entry->id);

        return new AjaxResult(1);
    }
}