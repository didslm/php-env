<?php

namespace Env;

use Env\Exception\MissingEnvironmentValue;

class Env
{
    public static function populate(object $obj, ?DotEnvLoader $dotEnvLoader = null): object
    {
        $reflector = new \ReflectionObject($obj);
        $dotEnvLoader ??= new DotEnvLoader();

        foreach ($reflector->getProperties() as $property) {
            if (self::isNestedObject($property)) {
                self::hydrateNestedObject($obj, $property, $dotEnvLoader);
                continue;
            }

            $envNameAttribute = self::getEnvNameAttribute($property);
            if ($envNameAttribute === null) {
                continue;
            }

            $envKey = $envNameAttribute->getValue();
            $default = $envNameAttribute->getDefault();

            $rawValue = self::resolveEnvValue($envKey, $default, $dotEnvLoader);
            $value = self::castValue($rawValue, $property->getType());

            self::assignValue($obj, $property, $value, $envKey);
        }

        return $obj;
    }

    protected static function isNestedObject(\ReflectionProperty $property): bool
    {
        return count($property->getAttributes(EnvObj::class)) > 0;
    }

    protected static function hydrateNestedObject(object $target, \ReflectionProperty $property, DotEnvLoader $loader): void
    {
        $attributeInstance = $property->getAttributes(EnvObj::class)[0]->newInstance();
        $targetClass = $attributeInstance->getTargetClass();

        $propertyType = $property->getType();

        if ($targetClass === null) {
            if ($propertyType instanceof \ReflectionNamedType && !$propertyType->isBuiltin()) {
                $targetClass = $propertyType->getName();
            } else {
                throw new \RuntimeException(sprintf(
                    'Unable to determine class for nested property %s::$%s. Please specify a target class in the EnvObj attribute.',
                    $property->getDeclaringClass()->getName(),
                    $property->getName()
                ));
            }
        }

        if (!class_exists($targetClass)) {
            throw new \RuntimeException(sprintf('Unable to hydrate nested object for %s::$%s. Class %s does not exist.', $property->getDeclaringClass()->getName(), $property->getName(), $targetClass));
        }

        $nested = new $targetClass();
        self::populate($nested, $loader);

        self::writeProperty($target, $property, $nested);
    }

    protected static function getEnvNameAttribute(\ReflectionProperty $property): ?EnvName
    {
        $attributes = $property->getAttributes(EnvName::class);

        if (count($attributes) === 0) {
            return null;
        }

        /** @var EnvName $envName */
        $envName = $attributes[0]->newInstance();

        return $envName;
    }

    protected static function resolveEnvValue(string $envKey, ?string $default, DotEnvLoader $loader): ?string
    {
        $envValue = self::normalizeEnvValue(getenv($envKey));
        if ($envValue !== null) {
            return $envValue;
        }

        $fileValue = self::normalizeEnvValue($loader->getEnvVar($envKey));
        if ($fileValue !== null) {
            return $fileValue;
        }

        return $default === null ? null : trim($default);
    }

    /**
     * @param mixed $rawValue
     */
    protected static function normalizeEnvValue($rawValue): ?string
    {
        if ($rawValue === false || $rawValue === null) {
            return null;
        }

        $value = is_string($rawValue) ? $rawValue : (string) $rawValue;
        $trimmed = trim($value);

        if ($trimmed === '' && $value !== '0') {
            return '';
        }

        return $trimmed;
    }

    protected static function castValue(?string $value, ?\ReflectionType $type)
    {
        if ($value === null || $type === null) {
            return $value;
        }

        if ($type instanceof \ReflectionUnionType) {
            return $value;
        }

        if (!$type instanceof \ReflectionNamedType) {
            return $value;
        }

        $typeName = $type->getName();

        if (!$type->isBuiltin()) {
            return $value;
        }

        switch ($typeName) {
            case 'int':
                return (int) $value;
            case 'float':
            case 'double':
                return (float) $value;
            case 'bool':
                return self::convertToBool($value);
            case 'string':
                return $value;
            default:
                return $value;
        }
    }

    protected static function convertToBool(string $value): bool
    {
        $normalized = strtolower($value);

        return in_array($normalized, ['1', 'true', 'on', 'yes'], true);
    }

    protected static function assignValue(object $target, \ReflectionProperty $property, $value, string $envKey): void
    {
        $type = $property->getType();

        if ($value === null && $type instanceof \ReflectionNamedType && !$type->allowsNull()) {
            throw new MissingEnvironmentValue(sprintf(
                'Environment variable "%s" is not defined and no default value was provided for %s::$%s.',
                $envKey,
                $property->getDeclaringClass()->getName(),
                $property->getName()
            ));
        }

        self::writeProperty($target, $property, $value);
    }

    protected static function writeProperty(object $target, \ReflectionProperty $property, $value): void
    {
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        $property->setValue($target, $value);
    }
}
