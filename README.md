# Php environment loader

The PHP Environment Variable Loader is a library designed to simplify the process of loading environment variables into PHP classes. This library provides a convenient and efficient way to read and store environment variables in your PHP application. By using this library, you can easily manage your application's configuration variables, such as API keys, database credentials, and other settings, without having to hardcode them into your codebase.

# Examples

```php

class Config 
{
    #[EnvObj]
    public DbConfig $mysqlConfig;
}


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

$allConfig = new Config();
Env::populate($allConfig);


```
