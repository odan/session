<?php

namespace Odan\Session\Adapter;

use Exception;
use RuntimeException;

/**
 * A secure PHP Session handler adapter.
 */
class PhpSecureSessionAdapter extends PhpSessionAdapter
{
    /**
     * Encryption and authentication key.
     *
     * @var string
     */
    protected $key = '';

    /**
     * Constructor.
     *
     * @param string $key The encryption key. Please use random_bytes(64).
     */
    public function __construct(string $key = '')
    {
        if (!extension_loaded('openssl')) {
            throw new RuntimeException(sprintf('You need the OpenSSL extension to use %s', __CLASS__));
        }

        if (!extension_loaded('mbstring')) {
            throw new RuntimeException(sprintf('You need the Multibytes extension to use %s', __CLASS__));
        }

        if (!empty($key) && strlen($key) < 64) {
            throw new RuntimeException('The session encryption key must contain be at least 64 bytes. Please use random_bytes(64).');
        }

        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        $data = parent::get($name, $default);

        return $data === $default ? $default : $this->decrypt($data, $this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        parent::set($name, $this->encrypt($value, $this->key));
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $values): void
    {
        foreach ($values as $key => $value) {
            $_SESSION[$key] = $this->encrypt($value, $this->key);
        }
    }

    /**
     * Encrypt and authenticate.
     *
     * @param string $data the data to encrypt
     * @param string $key the secret key
     *
     * @throws Exception
     *
     * @return string
     */
    protected function encrypt(string $data, string $key): string
    {
        $data = serialize($data);

        // AES block size in CBC mode
        $iv = random_bytes(16);

        // Encryption
        $cipherText = openssl_encrypt($data, 'AES-256-CBC', mb_substr($key, 0, 32, '8bit'), OPENSSL_RAW_DATA, $iv);

        // Authentication
        $hmac = hash_hmac('SHA256', $iv . $cipherText, mb_substr($key, 32, null, '8bit'), true);

        return $hmac . $iv . $cipherText;
    }

    /**
     * Authenticate and decrypt.
     *
     * @param string $data the encrypted data
     * @param string $key the secret key
     *
     * @return mixed the decrypted data
     */
    protected function decrypt(string $data, string $key)
    {
        $hmac = mb_substr($data, 0, 32, '8bit');
        $iv = mb_substr($data, 32, 16, '8bit');
        $cipherText = mb_substr($data, 48, null, '8bit');

        // Authentication
        $hmacNew = hash_hmac('SHA256', $iv . $cipherText, mb_substr($key, 32, null, '8bit'), true);
        if (!hash_equals($hmac, $hmacNew)) {
            throw new RuntimeException('Session authentication failed. Invalid hash value!');
        }

        // Decrypt
        $data = openssl_decrypt($cipherText, 'AES-256-CBC', mb_substr($key, 0, 32, '8bit'), OPENSSL_RAW_DATA, $iv);

        $data = unserialize($data);

        return $data;
    }
}
