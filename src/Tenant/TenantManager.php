<?php

namespace App\Tenant;

use App\Entity\Tenant;
use App\Repository\TenantRepository;

final class TenantManager
{
    private Tenant $currentTenant;

    public function __construct(
        private readonly TenantRepository $tenantRepository
    ){
    }

    public function getCurrentTenant(): Tenant
    {
        return $this->currentTenant;
    }

    /**
     * @throws UnknownTenantException
     */
    public function setCurrentTenantByDomain(string $domain): void
    {
        $tenant = $this->tenantRepository->findOneBy(['domain' => $domain]);
        if ($tenant === null) {
            throw new UnknownTenantException($domain);
        }

        $this->currentTenant = $tenant;
    }
}
