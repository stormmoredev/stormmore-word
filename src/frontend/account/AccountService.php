<?php

namespace frontend\account;

use authentication\StormUser;
use infrastructure\MediaFiles;
use Request;

readonly class AccountService
{
    public function __construct(
        private StormUser $stormUser,
        private MediaFiles $mediaFiles,
        private AccountStore $accountStore,
        private Request $request
    )
    {
    }

    public function updateProfilePhoto(): bool
    {
        $photo = $this->request->getFile('profile-photo');
        if (!$photo->isValidImage()) return false;

        $email = $this->stormUser->email;
        $name = sha1($email);
        $profile = $name . ".jpg";
        $this->mediaFiles->writeAsProfile($photo, $profile);
        $this->accountStore->updateProfile($this->stormUser->id, $profile);

        return true;
    }
}