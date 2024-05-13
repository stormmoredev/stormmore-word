<?php

namespace authentication\validators;

use authentication\UserRepository;
use IValidator, ValidatorResult;

readonly class UniqueUsernameValidator implements IValidator
{
    public function __construct(
        private UserRepository $userRepository)
    { }

    function validate(mixed $value, string $name, array $data, array $args): \ValidatorResult
    {
        if ($this->userRepository->getByUsername($value) != null)
            return new ValidatorResult(false, _("Username is already taken"));
        return new ValidatorResult();
    }
}