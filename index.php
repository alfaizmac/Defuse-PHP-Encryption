<?php
require_once 'vendor/autoload.php';
require_once 'config/database.php';
require_once 'includes/Database.php';
require_once 'includes/EncryptionService.php';

try {
    $db = new Database();
    $db->createTableIfNotExists();

    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Defuse PHP Encryption Demo</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .container { max-width: 800px; margin: 0 auto; }
            .card { 
                border: 1px solid #ddd; 
                padding: 20px; 
                margin: 20px 0; 
                border-radius: 8px;
                background-color: #f9f9f9;
            }
            h1 { color: #333; }
            a { 
                display: inline-block; 
                padding: 10px 15px; 
                background: #007bff; 
                color: white; 
                text-decoration: none; 
                border-radius: 4px; 
                margin: 5px;
            }
            a:hover { background: #0056b3; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Defuse PHP Encryption Demo</h1>
            <div class='card'>
                <h2>Single Table Implementation</h2>
                <p>This demo shows how to encrypt sensitive data before storing it in a database using Defuse PHP Encryption.</p>
                <p>All sensitive data is encrypted with a master key stored in the .env file.</p>
            </div>
            
            <div class='card'>
                <h3>Actions</h3>
                <p>
                    <a href='generate_key.php'>Generate Encryption Key</a>
                    <a href='insert_data.php'>Insert Sample Data</a>
                    <a href='retrieve_data.php'>View Decrypted Data</a>
                </p>
            </div>
            
            <div class='card'>
                <h3>Database Info</h3>";

    // Check if we have data
    $userCount = $db->getConnection()->query("SELECT COUNT(*) FROM users")->fetchColumn();

    echo "<p>Records in database: <strong>" . $userCount . "</strong></p>";

    if ($userCount == 0) {
        echo "<p>No data found. Insert some sample data to get started.</p>";
    }

    echo "</div></div></body></html>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>