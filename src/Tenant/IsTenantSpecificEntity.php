<?php

namespace App\Tenant;

use App\Entity\Tenant;
use Doctrine\ORM\Mapping as ORM;

trait IsTenantSpecificEntity
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tenant $tenant = null;

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }
}
