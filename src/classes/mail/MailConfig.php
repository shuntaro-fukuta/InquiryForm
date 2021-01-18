<?php

class MailConfig
{
    const CONFIG_FILENAME = 'mail_config.php';

    protected $config;

    public function __construct()
    {
        $config = include(CONFIG_DIR . DIR_SEP . self::CONFIG_FILENAME);
        if ($config === false) {
            throw new LogicException('mail config file "' . self::CONFIG_FILENAME . '" doesn\'t exists.');
        }

        $this->config = $config;
    }
}
