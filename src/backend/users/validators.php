<?php

use backend\UserStore;

function unique_username_validator($value, $name, $req) {
    $store = di(UserStore::class);
    if ($store->exist($value))
    return _("Username is already taken");
}

function repeat_password_validator($value, $name, $req) {
    if (!empty($req->password) && ($req->password !== $req->password2)) {
    return _("Password should be same");
    }
}