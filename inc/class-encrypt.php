<?php 

interface EncryptorInterface{
    public function encrypt(string $data): string;
    public function decrypt(string $data): string;
}


?>