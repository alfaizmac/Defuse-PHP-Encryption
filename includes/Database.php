<?php
class Database
{
    private $connection;

    public function __construct()
    {
        $config = DatabaseConfig::getConfig();

        try {
            $this->connection = new PDO(
                "mysql:host={$config['host']};dbname={$config['database']};charset=utf8",
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);

        return $stmt->execute($data);
    }

    public function select($table, $conditions = [], $limit = null)
    {
        $sql = "SELECT * FROM $table";

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "$column = :$column";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->connection->prepare($sql);

        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }

        if ($limit) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createTableIfNotExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone_encrypted VARCHAR(255) NOT NULL,
            ssn_encrypted VARCHAR(255) NOT NULL,
            credit_card_encrypted VARCHAR(255) NOT NULL,
            medical_info_encrypted TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->connection->exec($sql);
        return true;
    }
}
?>