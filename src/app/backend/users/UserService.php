<?php

namespace app\backend\users;

use app\authentication\PasswordHash;
use SessionStorage;

readonly class UserService
{
    function __construct (
        private SessionStorage $sessionStore,
        private UserStorage    $userStore
    ) { }

    public function add($user)
    {
        $user->password = PasswordHash::hash($user->password);
        return $this->userStore->insert($user);
    }

    public function update($user): void
    {
        $this->userStore->update($user);
    }
}