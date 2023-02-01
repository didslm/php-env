<?php

 namespace Env;

 use Attribute;

 #[Attribute(Attribute::TARGET_PROPERTY)]
class EnvName
{
    public function __construct(public string $value)
    {}

    public function getValue(): string
    {
        return $this->value;
    }
}