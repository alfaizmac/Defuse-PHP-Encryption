<?php
require_once 'vendor/autoload.php';

use Defuse\Crypto\Key;

try {
    $key = Key::createNewRandomKey();
    $keyAscii = $key->saveToAsciiSafeString();

    echo "Generated Encryption Key:\n";
    echo $keyAscii . "\n\n";
    echo "Copy this key to your .env file as ENCRYPTION_KEY\n";

    // Save to file for convenience
    file_put_contents('generated_key.txt', $keyAscii);

} catch (Exception $e) {
    echo "Error generating key: " . $e->getMessage() . "\n";
}
?>