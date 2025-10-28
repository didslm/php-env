<?php

namespace Env;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EnvObj
{
    public function __construct(public ?string $targetClass = null)
    {
    }

    public function getTargetClass(): ?string
    {
        return $this->targetClass;
    }
}
