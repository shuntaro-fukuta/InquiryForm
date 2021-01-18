<?php

require_once(CLASSES_DIR . DIR_SEP . 'mail' . DIR_SEP . 'MailConfig.php');

class MailHeaderConfig extends MailConfig
{
    private $To;
    private $From;
    private $ReplyTo;
    private $Sender;
    private $Charset;
    private $Subject;

    public function __construct(string $name)
    {
        parent::__construct();

        if (!isset($this->config['header'])) {
            throw new InvalidArgumentException('Mail config of "header" section doesn\'t exists.');
        }

        $headerConfig = $this->config['header'];
        if (!isset($headerConfig[$name])) {
            throw new InvalidArgumentException('Mail header properties of "' . $name . '" is undefined.');
        }

        $this->validateProperties($headerConfig[$name]);
        $this->setProperties($headerConfig[$name]);
    }

    protected function validateProperties(array $properties)
    {
        foreach ($properties as $propertyName => $value) {
            if (!property_exists($this, $propertyName)) {
                throw new LogicException('Invalid property "' . $propertyName . '".');
            }

            if (!is_string($value)) {
                throw new LogicException('value of type "' . $propertyName . '" must be string.');
            }
        }
    }

    protected function setProperties(array $properties)
    {
        foreach ($properties as $propertyName => $value) {
            $this->$propertyName = $value;
        }
    }

    public function setTo(string $to)
    {
        $this->To = $to;
    }

    public function getTo()
    {
        return $this->To;
    }

    public function getFrom()
    {
        return $this->From;
    }

    public function getReplyTo()
    {
        return $this->ReplyTo;
    }

    public function getSender()
    {
        return $this->Sender;
    }

    public function getSubject()
    {
        return $this->Subject;
    }

    public function getCharset()
    {
        return $this->Charset;
    }
}
