<?php

namespace Env;

class Env
{

    public static function populate(object &$obj): object
    {
        $reflector = new \ReflectionClass(get_class($obj));
        $dotEnvLoader = new DotEnvLoader();

        foreach ($reflector->getProperties() as $property) {

            if (count($property->getAttributes(EnvObj::class)) > 0) {
                $config = $property->getType()->getName();
                $config = new $config();

                self::populate($config);
                $obj->{$property->getName()} = $config;
            } else {
                [$attr] = $property->getAttributes(EnvName::class);
                [$envName] = $attr->getArguments();
                $obj->{$property->getName()} = empty(trim(getenv($envName))) === true ? $dotEnvLoader->getEnvVar($envName) : trim(getenv($envName));
            }


        }
        return $obj;
    }

}