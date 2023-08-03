<?php

namespace App\Tenant;

use App\Entity\Landlord\Tenant;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use SensitiveParameter;

class TenantDatabaseConnectionWrapper extends Connection
{
    public function __construct(#[SensitiveParameter] private array $params, Driver $driver, ?Configuration $config = null, ?EventManager $eventManager = null)
    {
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function selectDatabase(Tenant $tenant): void
    {
        if ($this->isConnected()) {
            $this->close();
        }

        $this->params['dbname'] = $tenant->getDatabase();
        parent::__construct($this->params, $this->_driver, $this->_config, $this->_eventManager);
    }

}
