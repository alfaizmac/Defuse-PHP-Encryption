<?php
require_once 'vendor/autoload.php';

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

class EncryptionService
{
    private $key;

    public function __construct()
    {
        $config = DatabaseConfig::getConfig();
        $keyString = $config['encryption_key'];

        if (empty($keyString)) {
            throw new Exception('Encryption key not found in configuration');
        }

        try {
            $this->key = Key::loadFromAsciiSafeString($keyString);
        } catch (Exception $e) {
            throw new Exception('Failed to load encryption key: ' . $e->getMessage());
        }
    }

    public function encrypt($plaintext)
    {
        if (empty($plaintext)) {
            return $plaintext;
        }

        try {
            return Crypto::encrypt($plaintext, $this->key);
        } catch (Exception $e) {
            throw new Exception('Encryption failed: ' . $e->getMessage());
        }
    }

    public function decrypt($ciphertext)
    {
        if (empty($ciphertext)) {
            return $ciphertext;
        }

        try {
            return Crypto::decrypt($ciphertext, $this->key);
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            // This is the specific error for wrong key or tampered data
            throw new Exception('Decryption failed - wrong key or corrupted data: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Decryption failed: ' . $e->getMessage());
        }
    }

    public function encryptUserData($data)
    {
        $encryptedData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_encrypted' => $this->encrypt($data['phone']),
            'ssn_encrypted' => $this->encrypt($data['ssn']),
            'credit_card_encrypted' => $this->encrypt($data['credit_card']),
            'medical_info_encrypted' => $this->encrypt($data['medical_info'])
        ];

        return $encryptedData;
    }

    public function decryptUserData($data)
    {
        $decryptedData = [
            'id' => $data['id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'created_at' => $data['created_at']
        ];

        // Decrypt each field with error handling
        try {
            $decryptedData['phone'] = $this->decrypt($data['phone_encrypted']);
        } catch (Exception $e) {
            $decryptedData['phone'] = 'DECRYPTION ERROR: ' . $e->getMessage();
        }

        try {
            $decryptedData['ssn'] = $this->decrypt($data['ssn_encrypted']);
        } catch (Exception $e) {
            $decryptedData['ssn'] = 'DECRYPTION ERROR: ' . $e->getMessage();
        }

        try {
            $decryptedData['credit_card'] = $this->decrypt($data['credit_card_encrypted']);
        } catch (Exception $e) {
            $decryptedData['credit_card'] = 'DECRYPTION ERROR: ' . $e->getMessage();
        }

        try {
            $decryptedData['medical_info'] = $this->decrypt($data['medical_info_encrypted']);
        } catch (Exception $e) {
            $decryptedData['medical_info'] = 'DECRYPTION ERROR: ' . $e->getMessage();
        }

        return $decryptedData;
    }

    // Test if the current key is valid
    public function testKey()
    {
        try {
            $testText = "Test encryption and decryption";
            $encrypted = $this->encrypt($testText);
            $decrypted = $this->decrypt($encrypted);

            return $decrypted === $testText;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>