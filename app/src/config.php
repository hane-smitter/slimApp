<?php

class Config
{
    private $dbSettings;
    private $errorSettings;

    public function __construct()
    {
        $this->dbSettings = [
            "dbname" => "slim_auth",
            "user" => "zaki",
            "host" => "mysql",
            "password" => "St*k*b*dh1.",
            "driver" => "pdo_mysql",
        ];
        $this->errorSettings = [
            "displayErrorDetails" => true,
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
