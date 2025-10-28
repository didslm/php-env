<?php

namespace Env;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EnvName
{
    public function __construct(
        public string $value,
        public ?string $default = null
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }
}
