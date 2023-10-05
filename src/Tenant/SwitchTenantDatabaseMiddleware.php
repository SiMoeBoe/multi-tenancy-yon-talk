<?php

namespace App\Tenant;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsMiddleware;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;


/**
 * We do not use this middleware in our example but using the TenantDatabaseConnectionWrapper.
 * Please check the README.md in chapter 8 to understand why.
 *
 * If you want to use this middleware instead of the ConnectionWrapper, please activate the following line and remove the early access from the wrap function.
 *
 * #[AsMiddleware(connections: ['default'])]
 */
class SwitchTenantDatabaseMiddleware implements Middleware
{

    public function __construct(private readonly TenantManager $tenantManager)
    {
    }

    public function wrap(Driver $driver): Driver
    {
        // @todo remove this return line if you want to use the middleware!
        return $driver;

        return new SwitchTenantDatabaseConnectionDriver($driver, $this->tenantManager);
    }
}
