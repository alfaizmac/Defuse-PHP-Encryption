<?php
class DatabaseConfig
{
    public static function getConfig()
    {
        // Parse .env file
        $envFile = __DIR__ . '/../.env';
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }

        $envContent = file_get_contents($envFile);
        $lines = explode("\n", $envContent);
        $config = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
                list($key, $value) = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }
        }

        return [
            'host' => $config['DB_HOST'] ?? 'localhost',
            'database' => $config['DB_NAME'] ?? 'testwebsite',
            'username' => $config['DB_USER'] ?? 'root',
            'password' => $config['DB_PASSWORD'] ?? 'alfaiz',
            'encryption_key' => $config['ENCRYPTION_KEY'] ?? ''
        ];
    }
}
?>