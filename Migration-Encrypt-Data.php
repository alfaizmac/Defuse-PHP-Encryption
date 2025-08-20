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

if (empty($encryptionKey)) {
    die("Error: ENCRYPTION_KEY not found in .env file\n");
}

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Load the encryption key
    $key = Key::loadFromAsciiSafeString($encryptionKey);

    echo "Starting encryption process...\n";

    // Fetch all users
    $stmt = $pdo->query("SELECT id, name, email FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalUsers = count($users);
    echo "Found $totalUsers users to encrypt\n";

    $successCount = 0;
    $errorCount = 0;

    // Prepare update statement
    $updateStmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");

    foreach ($users as $user) {
        try {
            // Encrypt name and email
            $encryptedName = Crypto::encrypt($user['name'], $key);
            $encryptedEmail = Crypto::encrypt($user['email'], $key);

            // Update the record
            $updateStmt->execute([
                ':name' => $encryptedName,
                ':email' => $encryptedEmail,
                ':id' => $user['id']
            ]);

            $successCount++;
            echo "Encrypted user ID: {$user['id']}\n";

        } catch (Exception $e) {
            $errorCount++;
            echo "Error encrypting user ID {$user['id']}: " . $e->getMessage() . "\n";
        }
    }

    echo "\nEncryption process completed!\n";
    echo "Successfully encrypted: $successCount users\n";
    echo "Failed to encrypt: $errorCount users\n";

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>