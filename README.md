# Overview

This project demonstrates how to implement Defuse PHP Encryption for securing sensitive data in a MySQL database. The solution encrypts data before storing it and decrypts it when retrieving for display.

## Installation Steps

1. Install Defuse PHP Encryption

```
composer require defuse/php-encryption
```

2. Create Database Table (The table will automatically CREATED if you insert data)

```
CREATE DATABASE IF NOT EXISTS Your_Database_Name;
USE Your_Database_Name;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone_encrypted VARCHAR(255) NOT NULL,
    ssn_encrypted VARCHAR(255) NOT NULL,
    credit_card_encrypted VARCHAR(255) NOT NULL,
    medical_info_encrypted TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

3. Run the key generator

```
php generate_key.php
```

4. Update .env file

Note you cannot create master key manually use the generated key.

```
DB_HOST=localhost
DB_NAME=Your_Database_Name
DB_USER=root
DB_PASSWORD=password
ENCRYPTION_KEY=your_encryption_key_here
```

### Now test the website locally

# Database Migration: Encrypt Existing Data

Use this script to encrypt existing plaintext data in your database. This is a one-time operation to secure data that was previously stored unencrypted.

### Usage Steps

1. You can backup of your data:

```
php backup_users.php
```

2. Run the migration script to encrypt existing data:

```
php Migration-Encrypt-Data.php
```

3. If you need to revert the encryption:

```
php decrypt_users.php
```

# ⚠️ CRITICAL SECURITY NOTICE: MASTER KEY PROTECTION

```
🚨 If You Lose Your Master Key
ALL ENCRYPTED DATA WILL BECOME PERMANENTLY INACCESSIBLE

🔒 No recovery possible: Defuse PHP Encryption uses strong encryption that cannot be broken without the key

⏳ Data is permanently locked: Even with immense computing power, decrypting without the key would take billions of years

🔐 No backdoors exist: Unlike some systems, there's no key recovery mechanism by design
```
