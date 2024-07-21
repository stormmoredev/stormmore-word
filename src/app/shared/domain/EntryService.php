<?php

namespace app\shared\domain;

use app\authentication\StormUser;
use infrastructure\AjaxResult;

readonly class EntryService
{
    public function __construct(
        private EntryRepository $entryRepository,
        private StormUser       $stormUser)
    {
    }

    public function vote(string $id): AjaxResult
    {
        $entry = $this->entryRepository->getById($id);
        $userId = $this->stormUser->id;

        if ($this->entryRepository->hasVoted($entry->id, $userId))
            return new AjaxResult(0);

        $this->entryRepository->insertVote($entry->id, $userId);
        $this->entryRepository->incrementVote($entry->id);

        return new AjaxResult(1);
    }

    public function removeVote(string $id): AjaxResult
    {
        $entry = $this->entryRepository->getById($id);
        $userId = $this->stormUser->id;

        if (!$this->entryRepository->hasVoted($entry->id, $userId))
            return new AjaxResult(0);

        $this->entryRepository->deleteVote($entry->id, $userId);
        $this->entryRepository->decrementVote($entry->id);

        return new AjaxResult(1);
    }
}