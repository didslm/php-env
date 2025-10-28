<?php

namespace Env;

use Env\Exception\DotEnvFileNotFound;

class DotEnvLoader
{
    public const DEFAULT_DOT_ENV_FILE = '.env';

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var string
     */
    protected $envFile;

    /**
     * @var array<string, string>
     */
    protected $envCache = [];

    /**
     * @var bool
     */
    protected $loaded = false;

    public function __construct(?string $rootPath = null, ?string $envFile = null)
    {
        $this->rootPath = $rootPath ?? $this->findRootPath();
        $this->envFile = $envFile ?? self::DEFAULT_DOT_ENV_FILE;

        if ($this->envFile === '') {
            throw new \InvalidArgumentException('Environment file name cannot be empty.');
        }
    }

    protected function findRootPath(): string
    {
        $currentDir = getcwd() ?: __DIR__;
        $fallbackDir = __DIR__;

        while (!file_exists($currentDir . DIRECTORY_SEPARATOR . self::DEFAULT_DOT_ENV_FILE)) {
            $parentDir = dirname($currentDir);
            if ($parentDir === $currentDir) {
                // Retry starting from the library path in case we were executed from a phar or a different cwd.
                $currentDir = $fallbackDir;
                break;
            }
            $currentDir = $parentDir;
        }

        while (!file_exists($currentDir . DIRECTORY_SEPARATOR . self::DEFAULT_DOT_ENV_FILE)) {
            $parentDir = dirname($currentDir);
            if ($parentDir === $currentDir) {
                throw new DotEnvFileNotFound();
            }
            $currentDir = $parentDir;
        }
        return $currentDir;
    }

    public function getEnvVar($key): ?string
    {
        $env = $this->load();

        return $env[$key] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function load(): array
    {
        if ($this->loaded) {
            return $this->envCache;
        }

        $envFilePath = $this->rootPath . DIRECTORY_SEPARATOR . $this->envFile;

        if (!file_exists($envFilePath)) {
            throw new DotEnvFileNotFound();
        }

        $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new \RuntimeException(sprintf('Unable to read environment file at %s', $envFilePath));
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$envKey, $envValue] = explode('=', $line, 2);
            $envKey = trim($envKey);
            $envValue = trim($envValue);

            if ($envKey === '') {
                continue;
            }

            // Strip optional surrounding quotes.
            $envValue = $this->stripQuotes($envValue);

            $this->envCache[$envKey] = $envValue;
        }

        $this->loaded = true;

        return $this->envCache;
    }

    protected function stripQuotes(string $value): string
    {
        if ($value === '') {
            return $value;
        }

        $first = $value[0];
        $last = $value[strlen($value) - 1];

        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
