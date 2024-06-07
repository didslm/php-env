<?php

namespace Env;

class DotEnvLoader
{
    const DEFAULT_DOT_ENV_FILE = '.env';

    function getEnvVar($key): ?string {
        $env = file(self::DEFAULT_DOT_ENV_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($env as $line) {
            list($envKey, $envValue) = explode('=', $line, 2);
            if (trim($envKey) === $key) {
                return trim($envValue);
            }
        }
        return null;
    }

}
