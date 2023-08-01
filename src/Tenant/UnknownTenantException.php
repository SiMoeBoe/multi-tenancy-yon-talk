<?php

namespace App\Tenant;

use Throwable;

class UnknownTenantException extends \Exception
{

    public function __construct(private readonly string $domain, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('No tenant configured for given domain "%s"', $this->domain), 0, $previous);
    }

    public function getDomain(): string
    {
        return $this->domain;
    }
}
