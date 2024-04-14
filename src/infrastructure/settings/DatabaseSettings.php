<?php

namespace infrastructure\settings;

use PDO;

class DatabaseSettings
{
    public $host;
    public $port;
    public $name;
    public $user;
    public $password;

    public function getConnectionString(): string
    {
        return "pgsql:host=$this->host;port=$this->port;dbname=$this->name";
    }

    public function getConnection(): PDO
    {
        return new PDO($this->getConnectionString(), $this->user, $this->password);
    }
}