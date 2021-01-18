<?php

require_once(CLASSES_DIR . DIR_SEP . 'mail' . DIR_SEP . 'MailConfig.php');

class MailAuthConfig extends MailConfig
{
    private $Host;
    private $Username;
    private $Password;
    private $Port;
    private $SMTPSecure;

    public function __construct(string $name)
    {
        parent::__construct();

        if (!isset($this->config['auth'])) {
            throw new InvalidArgumentException('Mail config of "auth" section doesn\'t exists.');
        }

        $authConfig = $this->config['auth'];
        if (!isset($authConfig[$name])) {
            throw new InvalidArgumentException('Mail auth properties of "' . $name . '" is undefined.');
        }

        $this->validateConfigProperties($authConfig[$name]);
        $this->setProperties($authConfig[$name]);
    }

    public function validateConfigProperties(array $properties)
    {
        foreach ($properties as $propertyName => $value) {
            if (!property_exists($this, $propertyName)) {
                throw new LogicException('Invalid config "' . $propertyName . '" defined in ' . self::CONFIG_FILENAME . '.');
            }

            if (!is_string($value) && !is_int($value)) {
                throw new LogicException('value of type "' . $propertyName . '" must be string or integer.');
            }
        }
    }

    private function setProperties(array $properties)
    {
        foreach ($properties as $propertyName => $value) {
            $this->$propertyName = $value;
        }
    }

    public function getHost()
    {
        return $this->Host;
    }

    public function getUsername()
    {
        return $this->Username;
    }

    public function getPassword()
    {
        return $this->Password;
    }

    public function getPort()
    {
        return $this->Port;
    }

    public function getSMTPSecure()
    {
        return $this->SMTPSecure;
    }
}
