<?php

/** @noinspection PhpUnused  */

namespace authentication\validators;

use IValidator, ValidatorResult;

class RepeatedPasswordValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, array $args): ValidatorResult
    {
        if (!empty($data['password']) && ($data['password'] !== $data['password2']))
            return new ValidatorResult(false, _("Passwords should be the same"));
        return new ValidatorResult();
    }
}