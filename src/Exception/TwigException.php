<?php

namespace App\Exception;

use Exception;

class TwigException extends Exception
{
    public function __construct(Exception $ex)
    {
        parent::__construct($ex->getMessage(), $ex->getCode(), $ex);
    }
}
