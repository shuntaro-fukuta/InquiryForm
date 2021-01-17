<?php

class Database
{
    private $pdo;

    public function __construct(DBConfig $config)
    {
        if (is_null($config)) {
            throw new InvalidArgumentException('DBConfig must not be null.');
        }

        $driver = $config->getDriver();
        $database = $config->getDatabase();
        $host = $config->getHost();
        $username = $config->getUsername();
        $password = $config->getPassword();
        $charset = $config->getCharset();

        $this->pdo = new PDO(
            $driver . ':dbname=' . $database . ';host=' . $host . ';charset=' . $charset,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

    public function insert(string $tableName, array $params)
    {
        $columns = array_keys($params);
        $placeholders = [];
        foreach ($columns as $column) {
            $placeholders[] = ':' . $column;
        }

        $sql = 'INSERT INTO ' . $tableName . ' ( ';
        $sql .= implode(', ' , $columns);
        $sql .= ') VALUES (';
        $sql .= implode(', ', $placeholders);
        $sql .= ');';

        $pstmt = $this->pdo->prepare($sql);
        return $pstmt->execute($params);
    }
}
