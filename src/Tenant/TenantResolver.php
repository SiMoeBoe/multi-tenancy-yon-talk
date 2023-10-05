<?php

namespace App\Tenant;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

readonly final class TenantResolver
{

    public function __construct(
        private TenantManager $tenantManager
    ){
    }

    #[AsEventListener]
    public function setTenantByDomain(RequestEvent $requestEvent): void
    {
          $this->tenantManager->setCurrentTenantByDomain($requestEvent->getRequest()->getHttpHost());
    }
}
