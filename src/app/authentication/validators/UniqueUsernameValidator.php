<?php

namespace app\authentication\validators;

use app\authentication\UserRepository;
use IValidator;
use ValidatorResult;

readonly class UniqueUsernameValidator implements IValidator
{
    public function __construct(
        private UserRepository $userRepository)
    { }

    function validate(mixed $value, string $name, array $data, mixed $args): \ValidatorResult
    {
        if ($this->userRepository->getByUsername($value) != null)
            return new ValidatorResult(false, _("Username is already taken"));
        return new ValidatorResult();
    }
}