<?php

/** @noinspection PhpUnused  */

namespace app\authentication\validators;

use IValidator;
use ValidatorResult;

class RepeatedPasswordValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!empty($data['password']) && ($data['password'] !== $data['password2']))
            return new ValidatorResult(false, _("Passwords should be the same"));
        return new ValidatorResult();
    }
}