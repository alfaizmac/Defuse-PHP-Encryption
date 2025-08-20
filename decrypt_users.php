<?php
require_once 'vendor/autoload.php';

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

// Load environment variables
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
    foreach ($envVars as $key => $value) {
        putenv("$key=$value");
    }
}

// Database configuration
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'testwebsite';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: 'alfaiz';
$encryptionKey = getenv('ENCRYPTION_KEY') ?: '';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $key = Key::loadFromAsciiSafeString($encryptionKey);

    $stmt = $pdo->query("SELECT id, name, email FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updateStmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");

    foreach ($users as $user) {
        try {
            $decryptedName = Crypto::decrypt($user['name'], $key);
            $decryptedEmail = Crypto::decrypt($user['email'], $key);

            $updateStmt->execute([
                ':name' => $decryptedName,
                ':email' => $decryptedEmail,
                ':id' => $user['id']
            ]);

            echo "Decrypted user ID: {$user['id']}\n";

        } catch (Exception $e) {
            echo "Error decrypting user ID {$user['id']}: " . $e->getMessage() . "\n";
        }
    }

    echo "Decryption process completed!\n";

} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>