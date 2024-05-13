<?php

namespace authentication\validators;

use authentication\UserRepository;
use IValidator, ValidatorResult;

class UniqueEmailValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, array $args): ValidatorResult
    {
        $store = di(UserRepository::class);
        if ($store->getByEmail($value) != null)
            return new ValidatorResult(false, _("Email already exist"));
        return new ValidatorResult();
    }
}