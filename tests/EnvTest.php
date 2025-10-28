<?php

namespace Tests;

use Env\Env;
use Env\EnvName;
use Env\EnvObj;
use Env\Exception\MissingEnvironmentValue;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ([
            'TEST_INT',
            'TEST_FLOAT',
            'TEST_BOOL',
            'MISSING_WITH_DEFAULT',
            'CUSTOM_NESTED',
            'REQUIRED_WITHOUT_DEFAULT',
        ] as $variable) {
            putenv($variable);
        }
    }

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

    public function testShouldUseDefaultValueWhenEnvVariableIsMissing()
    {
        $config = new DefaultConfig();

        Env::populate($config);

        $this->assertSame('fallback', $config->value);
    }

    public function testShouldCastScalarValuesBasedOnPropertyType()
    {
        putenv('TEST_INT=42');
        putenv('TEST_FLOAT=13.37');
        putenv('TEST_BOOL=true');

        $config = new TypedConfig();

        Env::populate($config);

        $this->assertSame(42, $config->intValue);
        $this->assertSame(13.37, $config->floatValue);
        $this->assertTrue($config->boolValue);
    }

    public function testShouldSupportCustomClassOnEnvObj()
    {
        putenv('CUSTOM_NESTED=custom');

        $config = new CustomContainer();
        Env::populate($config);

        $this->assertInstanceOf(CustomNested::class, $config->nested);
        $this->assertSame('custom', $config->nested->value);
    }

    public function testShouldThrowHelpfulExceptionWhenValueIsMissing()
    {
        $this->expectException(MissingEnvironmentValue::class);

        $config = new RequiredConfig();
        Env::populate($config);
    }
}

class DefaultConfig
{
    #[EnvName('MISSING_WITH_DEFAULT', default: 'fallback')]
    public string $value;
}

class TypedConfig
{
    #[EnvName('TEST_INT')]
    public int $intValue;

    #[EnvName('TEST_FLOAT')]
    public float $floatValue;

    #[EnvName('TEST_BOOL')]
    public bool $boolValue;
}

class CustomNested
{
    #[EnvName('CUSTOM_NESTED')]
    public string $value;
}

class CustomContainer
{
    #[EnvObj(CustomNested::class)]
    public CustomNested $nested;
}

class RequiredConfig
{
    #[EnvName('REQUIRED_WITHOUT_DEFAULT')]
    public string $value;
}
