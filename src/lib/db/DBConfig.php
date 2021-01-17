<?php

class DBConfig
{
    const DB_CONFIG_FILENAME = 'database_config.php';

    private $driver;
    private $database;
    private $host;
    private $username;
    private $password;
    private $charset;

    private $configs = [
        'driver',
        'database',
        'host',
        'username',
        'password',
        'charset',
    ];

    public function __construct(string $id = 'default')
    {
        $config = require(CONFIG_DIR . DIR_SEP . self::DB_CONFIG_FILENAME);
        if ($config === false) {
            throw new LogicException('mail config file "' . self::DB_CONFIG_FILENAME . '" doesn\'t exists.');
        }

        if (!isset($config[$id])) {
            throw new InvalidArgumentException('Database setting of "' . $id . '" is undefined.');
        }

        $this->validateConfig($config[$id]);

        foreach ($config[$id] as $configName => $value) {
            $this->$configName = $value;
        }
    }

    private function validateConfig(?array $config)
    {
        if (is_null($config)) {
            throw new LogicException('Database setting must be an array.');
        }

        foreach ($this->configs as $configName) {
            if (!isset($config[$configName])) {
                throw new LogicException('Database setting "' . $configName . '" is undefined.');
            }

            if (!is_string($config[$configName])) {
                throw new LogicException('Database setting "' . $configName . '" must be type of string.');
            }
        }
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getCharset()
    {
        return $this->charset;
    }
}
