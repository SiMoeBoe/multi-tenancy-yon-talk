<?php

namespace App\Tenant;

use App\Entity\Landlord\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

readonly final class TenantResolver
{

    public function __construct(
        private TenantManager $tenantManager,
        private Security $security
    )
    {
    }

    #[AsEventListener]
    public function setTenantByRequest(RequestEvent $requestEvent): void
    {
        $this->tenantManager->setCurrentTenantByDomain($requestEvent->getRequest()->getHttpHost());

        $user = $this->security->getUser();
        if ($user instanceof User) {
            if (!$user->getTenants()->contains($this->tenantManager->getCurrentTenant())) {
                $this->security->logout(false);
                throw new AuthenticationCredentialsNotFoundException();
            }
        }
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
