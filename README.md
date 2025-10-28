# PHP Environment Variable Loader

The PHP Environment Variable Loader is a library that simplifies the process of loading environment variables into PHP classes. It provides a convenient and efficient way to read and store environment variables in your PHP application. By using this library, you can easily manage your application's configuration variables, such as API keys, database credentials, and other settings, without hardcoding them into your codebase.

## Installation

To install the library, you can use Composer. If you don't have Composer installed, please follow the instructions on the official website.

Run the following command to install the PHP Environment Variable Loader library:
```bash
composer require didslm/env
```
## Usage

To use the library in your PHP code, you need to instantiate the `Didslm\Env` class and call the `populate` method:

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

### Advanced Usage

- **Default values**  
  Provide a fallback that is used when the environment variable (and `.env`) do not provide a value.
  ```php
  class Defaults
  {
      #[EnvName('APP_REGION', default: 'eu-west-1')]
      public string $region;
  }
  ```

- **Strong typing**  
  Scalar properties are automatically converted to the correct PHP type (`int`, `float`, `bool`, `string`). For booleans the loader understands `1/0`, `true/false`, `on/off`, and `yes/no`.

- **Nested configuration objects**  
  Use `EnvObj` to hydrate nested objects. Optionally provide the class name if it differs from the property type.
  ```php
  class Services
  {
      #[EnvObj(ServiceConfig::class)]
      public ServiceConfig $billing;
  }
  ```

- **Custom `.env` location**  
  `Env::populate($config, new DotEnvLoader($projectRoot, 'custom.env'));` lets you point the loader to a specific directory or file without changing the default behaviour.

- **Helpful errors**  
  When a non-nullable property cannot be populated, the library now throws `Env\Exception\MissingEnvironmentValue` with clear information about the missing variable.

## Troubleshooting

If you encounter any issues while using the library, please check the troubleshooting section of the documentation. 
If your issue is not covered in the troubleshooting section, please [create a new issue](https://github.com/didslm/php-env/issues/new) on the GitHub repository.

## Contributing

If you would like to contribute to the PHP Environment Variable Loader library, please read the [contributing guidelines](https://github.com/didslm/php-env/blob/main/CONTRIBUTING.md) before submitting a pull request.

## Contact

If you have any questions or feedback on the library, please feel free to [contact me](https://linkedin.com/in/diarselimi).

## License

The PHP Environment Variable Loader library is open-source software licensed under the [MIT license](https://github.com/didslm/php-env/blob/main/LICENSE).
