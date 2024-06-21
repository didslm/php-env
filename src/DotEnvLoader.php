<?php

namespace Env;

use Env\Exception\DotEnvFileNotFound;

class DotEnvLoader
{
    const DEFAULT_DOT_ENV_FILE = '.env';

    protected $rootPath;

    public function __construct()
    {
        $this->rootPath = $this->findRootPath();
    }

    protected function findRootPath(): string
    {
        $currentDir = __DIR__;
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
        $envFilePath = $this->rootPath . DIRECTORY_SEPARATOR . self::DEFAULT_DOT_ENV_FILE;
        $env = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($env as $line) {
            list($envKey, $envValue) = explode('=', $line, 2);
            if (trim($envKey) === $key) {
                return trim($envValue);
            }
        }
        return null;
    }
}
