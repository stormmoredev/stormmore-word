<?php

namespace app\authentication\validators;

use app\authentication\UserRepository;
use IValidator;
use ValidatorResult;

class UniqueEmailValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        $store = di(UserRepository::class);
        if ($store->getByEmail($value) != null)
            return new ValidatorResult(false, _("Email already exist"));
        return new ValidatorResult();
    }
}