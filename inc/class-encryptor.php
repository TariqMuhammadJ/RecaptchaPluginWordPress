<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Encryptor')) {
    require_once __DIR__ . '/class-encrypt.php';

    class Encryptor implements EncryptorInterface {
        private string $key;
        private string $method;
        private int $ivLength;

        public function __construct(string $key, string $method = 'AES-256-CBC') {
            $this->key = $key;
            $this->method = $method;
            $this->ivLength = openssl_cipher_iv_length($method);
        }

        /**
         * Encrypts a string using AES-256-CBC and returns base64-encoded result.
         */
        public function encrypt(string $data): string {
            $iv = openssl_random_pseudo_bytes($this->ivLength);
            $encrypted = openssl_encrypt($data, $this->method, $this->key, 0, $iv);

            if ($encrypted === false) {
                throw new RuntimeException('Encryption failed.');
            }

            return base64_encode($iv . $encrypted);
        }

        /**
         * Decrypts a base64-encoded string using AES-256-CBC.
         */
        public function decrypt(string $data): string {
            $decoded = base64_decode($data, true);

            if ($decoded === false) {
                throw new RuntimeException('Base64 decoding failed.');
            }

            $iv = substr($decoded, 0, $this->ivLength);
            $encryptedData = substr($decoded, $this->ivLength);

            $decrypted = openssl_decrypt($encryptedData, $this->method, $this->key, 0, $iv);

            if ($decrypted === false) {
                throw new RuntimeException('Decryption failed.');
            }

            return $decrypted;
        }
    }
}
?>
