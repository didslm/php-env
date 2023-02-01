<?php

namespace Env;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EnvObj
{
    public function __construct(public object $value){}
}