<?php

namespace App\DataFixtures;

use App\Entity\Landlord\Tenant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TenantFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['landlord'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getTenants() as $tenantData) {
            $tenant = new Tenant();
            $tenant
                ->setName($tenantData['name'])
                ->setDomain($tenantData['domain'])
                ->setDatabase($tenantData['database'])
            ;
            $manager->persist($tenant);
            $this->addReference($tenantData['domain'], $tenant);
        }

        $manager->flush();
    }

    private function getTenants(): iterable
    {
        yield [
            'name' => 'Tenant 1',
            'domain' => 'tenant1.localhost',
            'database' => 'tenant'
        ];

        yield [
            'name' => 'Tenant 2',
            'domain' => 'tenant2.localhost',
            'database' => 'tenant2'
        ];

        yield [
            'name' => 'App Tenant 1',
            'domain' => 'tenant1.app.localhost',
            'database' => 'apptenant1'
        ];

        yield [
            'name' => 'App Tenant 2',
            'domain' => 'tenant2.app.localhost',
            'database' => 'apptenant2'
        ];
    }
}
