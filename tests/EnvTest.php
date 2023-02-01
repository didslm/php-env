<?php

namespace Tests;

use Env\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function testShouldFillDataFromEnvVariables()
    {
        $config = new DbConfig();
        Env::populate($config);

        $this->assertEquals('some_host_123', $config->dbHost);
        $this->assertEmpty($config->database);
    }

    public function testShouldFillDataInsideObjects()
    {
        $config = new Config();
        Env::populate($config);

        $this->assertEquals('some_host_123', $config->mysqlConfig->dbHost);
        $this->assertEmpty($config->mysqlConfig->database);
        $this->assertNotEmpty($config->mysqlConfig);
    }

}