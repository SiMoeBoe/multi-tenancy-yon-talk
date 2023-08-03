<?php

namespace App\Tenant;

use Throwable;

class NoTenantDatabaseConnectionException extends \Exception
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(
            sprintf("No tenant connection wrapper used. Please add %s as 'wrapper_class' to your tenant/default configuration", TenantDatabaseConnectionWrapper::class),
            0,
            $previous
        );
    }
}
