<?php

namespace App\Tenant;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class TenantAwareFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!in_array(IsTenantSpecificEntity::class, $targetEntity->getReflectionClass()->getTraitNames())) {
            return '';
        }

        if ($this->getParameter('tenant') === "''") {
            throw new NoTenantSpecifiedException();
        }

        return $targetTableAlias . '.tenant_id = ' . $this->getParameter('tenant');
    }
}
