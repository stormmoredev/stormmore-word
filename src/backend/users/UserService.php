<?php

namespace backend;

use SessionStore;

readonly class UserService
{
    function __construct (
        private SessionStore $sessionStore,
        private UserStore    $userStore
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