# php-env
A library which helps you load your environment variables into php classes.

```php

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

$config = new DbConfig();
//The next line will populate the class properties with the env variables if they exist.
Env::populate($config);

```
