<?php

class Config
{
    private $dbSettings;
    private $errorSettings;

    public function __construct()
    {
        $this->dbSettings = [
            "dbname" => $_ENV["DB_NAME"],
            "user" => $_ENV["DB_USER"],
            "host" => $_ENV["DB_HOST"],
            "password" => $_ENV["DB_PASSWORD"],
            "driver" => $_ENV["DB_DRIVER"],
        ];
        $this->errorSettings = [
            "displayErrorDetails" => $_ENV["APP_DEBUG"] === "development",
            "logErrors" => true,
            "logErrorDetails" => true,
        ];
    }

    public function getDbSettings()
    {
        return $this->dbSettings;
    }
    public function getErrorSttings()
    {
        return $this->errorSettings;
    }
}
