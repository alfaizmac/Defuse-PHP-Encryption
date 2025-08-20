<?php
// backup_users.php
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'testwebsite';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: 'alfaiz';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);

    // Create backup table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users_backup LIKE users");
    $pdo->exec("TRUNCATE TABLE users_backup");
    $pdo->exec("INSERT INTO users_backup SELECT * FROM users");

    echo "Backup created successfully: users_backup\n";

} catch (PDOException $e) {
    die("Backup failed: " . $e->getMessage() . "\n");
}
?>