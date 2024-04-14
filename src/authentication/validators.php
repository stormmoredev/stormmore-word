<?php

namespace authentication;

function unique_username_validator($value, $name, $req) {
    $store = di(UserRepository::class);
    if ($store->getByUsername($value) != null)
        return _("Username is already taken");
}

function unique_email_validator($value, $name, $req) {
    $store = di(UserRepository::class);
    if ($store->getByEmail($value) != null)
        return _("Email already exist");
}

function repeat_password_validator($value, $name, $req) {
    if (!empty($req->password) && ($req->password !== $req->password2)) {
        return _("Passwords should be the same");
    }
}