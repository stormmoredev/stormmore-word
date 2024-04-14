<?php

namespace authentication;

use Cookies;
use Hybridauth\Storage\StorageInterface;

class HybridauthCookieStorage implements StorageInterface
{
    public function get($key)
    {
        if (!Cookies::has('hybridauth')) {
            return null;
        }

        $obj = json_decode(Cookies::get('hybridauth'));
        $props = get_object_vars($obj);
        if (array_key_exists($key, $props)) {
            return $props[$key];
        }
        return null;
    }

    public function set($key, $value)
    {
        if (!Cookies::has('hybridauth')) {
            Cookies::set('hybridauth', '{}');
        }

        $obj = json_decode(Cookies::get('hybridauth'));
        $obj->$key = $value;
        Cookies::set('hybridauth', json_encode($obj));
    }

    public function clear()
    {
        Cookies::delete('hybridauth');
    }

    public function delete($key)
    {
    }

    public function deleteMatch($key)
    {
    }
}