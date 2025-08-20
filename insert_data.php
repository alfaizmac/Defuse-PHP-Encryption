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

    // Sample data to insert
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'ssn' => '123-45-6789',
        'credit_card' => '4111111111111111',
        'medical_info' => 'Patient has allergy to penicillin'
    ];

    // Encrypt sensitive fields
    $encryptedUserData = $encryption->encryptUserData($userData);

    // Insert into database
    $result = $db->insert('users', $encryptedUserData);

    if ($result) {
        echo "Data inserted successfully!\n";
        echo "User ID: " . $db->getConnection()->lastInsertId() . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>