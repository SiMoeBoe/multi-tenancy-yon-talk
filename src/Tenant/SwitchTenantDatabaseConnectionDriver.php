<?php

namespace App\Tenant;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use SensitiveParameter;

class SwitchTenantDatabaseConnectionDriver extends AbstractDriverMiddleware
{

    public function __construct(Driver $wrappedDriver, private readonly TenantManager $tenantManager)
    {
        parent::__construct($wrappedDriver);
    }

    public function connect(#[SensitiveParameter] array $params): Connection
    {
        $params['dbname'] = $this->tenantManager->getCurrentTenant()->getDatabase();

        return parent::connect($params);
    }
}
