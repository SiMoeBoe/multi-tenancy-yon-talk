<?php

namespace App\DataFixtures;

use App\Entity\Tenant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TenantFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getTenants() as $tenantData) {
            $tenant = new Tenant();
            $tenant
                ->setName($tenantData['name'])
                ->setDomain($tenantData['domain']);
            $manager->persist($tenant);
            $this->addReference($tenantData['domain'], $tenant);
        }

        $manager->flush();
    }

    private function getTenants(): iterable
    {
        yield [
            'name' => 'Tenant 1',
            'domain' => 'tenant1.localhost'
        ];

        yield [
            'name' => 'Tenant 2',
            'domain' => 'tenant2.localhost'
        ];
    }
}
