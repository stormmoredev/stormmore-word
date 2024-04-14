<?php

namespace authentication;

use infrastructure\settings\Settings;

class UserSecret
{
    public function __construct(
        private Settings $settings
    ) { }

    public function encrypt(string $message): string
    {
        if (mb_strlen($this->settings->secretKey, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RangeException('Key is not the correct size (must be 32 bytes).');
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $this->settings->secretKey
            )
        );
        sodium_memzero($message);
        return $cipher;
    }

    function decrypt(string $encrypted): string
    {
        $decoded = base64_decode($encrypted);
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $this->settings->secretKey
        );
        if (!is_string($plain)) {
            throw new \Exception('Invalid MAC');
        }
        sodium_memzero($ciphertext);
        return $plain;
    }
}