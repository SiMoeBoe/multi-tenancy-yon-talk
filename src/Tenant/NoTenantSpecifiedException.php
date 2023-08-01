<?php

namespace App\Tenant;

use Exception;
use Throwable;

class NoTenantSpecifiedException extends Exception
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("No tenant specified. Maybe no '--tenant' option added to your command or no host given?", 0, $previous);
    }
}
