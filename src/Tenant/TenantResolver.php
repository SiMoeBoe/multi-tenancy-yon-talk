<?php

namespace App\Tenant;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

readonly final class TenantResolver
{

    public function __construct(
        private TenantManager $tenantManager
    ){
    }

    #[AsEventListener]
    public function setTenantByRequest(RequestEvent $requestEvent): void
    {
          $this->tenantManager->setCurrentTenantByDomain($requestEvent->getRequest()->getHttpHost());
    }

    #[AsEventListener]
    public function setTenantByCLI(ConsoleCommandEvent $commandEvent): void
    {
        $input = $commandEvent->getInput();
        if (!$input->hasOption('tenant')) {
            $commandEvent->getCommand()->addOption('tenant', null, InputOption::VALUE_REQUIRED, 'Tenant domain');
            $input->bind($commandEvent->getCommand()->getDefinition());
        }

        if ($input->hasOption('tenant') && $input->getOption('tenant') !== null) {
            $this->tenantManager->setCurrentTenantByDomain($input->getOption('tenant'));
        }
    }
}
