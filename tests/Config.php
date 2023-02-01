<?php

namespace Tests;

use Env\EnvObj;

class Config
{
    #[EnvObj]
    public DbConfig $mysqlConfig;
}