<?php

namespace App\Tenant;

use App\Entity\Landlord\Tenant;
use App\Repository\TenantRepository;
use Doctrine\ORM\EntityManagerInterface;

final class TenantManager
{
    private Tenant $currentTenant;

    public function __construct(
        private readonly TenantRepository $tenantRepository,
        private readonly EntityManagerInterface $entityManager
    ){
    }

    public function getCurrentTenant(): Tenant
    {
        return $this->currentTenant;
    }

    private function setCurrentTenant(Tenant $tenant): void
    {
        $this->currentTenant = $tenant;

        $connection = $this->entityManager->getConnection();
        if(!$connection instanceof TenantDatabaseConnectionWrapper) {
            throw new NoTenantDatabaseConnectionException();
        }

        $connection->selectDatabase($tenant);
    }

    /**
     * @throws UnknownTenantException|NoTenantDatabaseConnectionException
     */
    public function setCurrentTenantByDomain(string $domain): void
    {
        $tenant = $this->tenantRepository->findOneBy(['domain' => $domain]);
        if ($tenant === null) {
            throw new UnknownTenantException($domain);
        }

        $this->setCurrentTenant($tenant);
    }
}
