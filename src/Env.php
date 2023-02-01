<?php

namespace Env;

class Env
{

    public static function populate(object &$obj): object
    {
        $reflector = new \ReflectionClass(get_class($obj));

        foreach ($reflector->getProperties() as $property) {
            [$attr] = $property->getAttributes(EnvName::class);
            [$envName] = $attr->getArguments();

            $obj->{$property->getName()} = getenv($envName);

        }
        return $obj;
    }

}