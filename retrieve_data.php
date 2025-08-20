<?php
require_once 'vendor/autoload.php';
require_once 'config/database.php';
require_once 'includes/Database.php';
require_once 'includes/EncryptionService.php';

try {
    $db = new Database();
    $encryption = new EncryptionService();

    // Create table if it doesn't exist
    $db->createTableIfNotExists();


    // Retrieve encrypted data
    $users = $db->select('users');

    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Encrypted Data Viewer</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .user-card { 
                border: 1px solid #ddd; 
                padding: 20px; 
                margin: 10px 0; 
                border-radius: 8px;
                background-color: #f9f9f9;
            }
            .sensitive { 
                color: #d9534f; 
                background-color: #fff3f3;
                padding: 5px;
                border-radius: 4px;
                margin: 5px 0;
            }
            .error {
                color: #a94442;
                background-color: #f2dede;
                padding: 5px;
                border-radius: 4px;
                margin: 5px 0;
            }
            h1 { color: #333; }
            .key-status { 
                padding: 10px; 
                margin: 10px 0; 
                border-radius: 4px;
            }
            .key-valid { background-color: #dff0d8; color: #3c763d; }
            .key-invalid { background-color: #f2dede; color: #a94442; }
        </style>
    </head>
    <body>
        <h1>Decrypted User Data</h1>
        <p><a href='insert_data.php'>Insert More Data</a> | <a href='index.php'>Home</a></p>";


    if (empty($users)) {
        echo "<p>No users found. <a href='insert_data.php'>Insert some data first</a>.</p>";
    } else {
        foreach ($users as $user) {
            // Decrypt sensitive fields
            $decryptedUser = $encryption->decryptUserData($user);

            echo "<div class='user-card'>";
            echo "<h3>" . htmlspecialchars($decryptedUser['name']) . "</h3>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($decryptedUser['email']) . "</p>";

            // Display each field with appropriate styling
            $fields = [
                'phone' => 'Phone',
                'ssn' => 'SSN',
                'credit_card' => 'Credit Card',
                'medical_info' => 'Medical Info'
            ];

            foreach ($fields as $field => $label) {
                if (strpos($decryptedUser[$field], 'DECRYPTION ERROR:') === 0) {
                    echo "<div class='error'><strong>$label:</strong> " . htmlspecialchars($decryptedUser[$field]) . "</div>";
                } else {
                    echo "<div class='sensitive'><strong>$label:</strong> " . htmlspecialchars($decryptedUser[$field]) . "</div>";
                }
            }

            echo "<p><small>Created: " . htmlspecialchars($decryptedUser['created_at']) . "</small></p>";
            echo "</div>";
        }
    }

    echo "</body></html>";

} catch (Exception $e) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .error { color: #a94442; background-color: #f2dede; padding: 15px; border-radius: 4px; }
        </style>
    </head>
    <body>
        <h1>Error</h1>
        <div class='error'>" . htmlspecialchars($e->getMessage()) . "</div>
        <p>Check your encryption key in the .env file and make sure it matches the key used to encrypt the data.</p>
        <p><a href='generate_key.php'>Generate a new key</a></p>
    </body>
    </html>";
}
?>