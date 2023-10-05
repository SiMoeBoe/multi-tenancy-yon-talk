<?php

namespace App\Tenant;

use Doctrine\ORM\Mapping as ORM;

trait IsTenantSpecificEntity
{
    #[ORM\Column(nullable: false)]
    private ?int $tenant_id = null;

    public function getTenantId(): ?int
    {
        return $this->tenant_id;
    }

    public function setTenantId(?int $tenant): static
    {
        $this->tenant_id = $tenant;

        return $this;
    }
}
