<?php

namespace Env\Exception;

class DotEnvFileNotFound extends \Exception
{
    protected $message = "No .env file found in the project root.";
}