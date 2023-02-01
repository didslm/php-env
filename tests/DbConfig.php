<?php
namespace Tests;

use Env\EnvName;

class DbConfig
{
    #[EnvName('DB_HOST')]
    public string $dbHost;
    #[EnvName('DB_USER')]
    public string $username;
    #[EnvName('DB_PASSWORD')]
    public string $password;
    #[EnvName('DB_DATABASE')]
    public string $database;

}