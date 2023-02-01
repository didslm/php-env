<?php

namespace Tests;

use Env\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function testShouldFillDataFromEnvVariables()
    {
        $db = getenv('DB_HOST');

        $config = new DbConfig();
        Env::populate($config);

        $this->assertEquals($config->dbHost, $db);
        $this->assertEmpty($config->database);
    }


}