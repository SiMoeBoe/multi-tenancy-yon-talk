<?php

namespace App\DataFixtures;

use App\Entity\Landlord\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public static function getGroups(): array
    {
        return ['landlord'];
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->getUserForTenants('firstuser'));
        $manager->persist($this->getUserForTenants('tenant1', [$this->getReference('tenant1.localhost'), $this->getReference('tenant1.app.localhost')]));
        $manager->persist($this->getUserForTenants('tenant2', [$this->getReference('tenant2.localhost'), $this->getReference('tenant2.app.localhost')]));
        $manager->persist($this->getUserForTenants('all', [
            $this->getReference('tenant1.localhost'),
            $this->getReference('tenant2.localhost'),
            $this->getReference('tenant1.app.localhost'),
            $this->getReference('tenant2.app.localhost'),
        ]));

        $manager->flush();
    }

    private function getUserForTenants(string $mailPrefix, array $tenants = []): User
    {
        $user = new User();
        $user->setEmail($mailPrefix . '@test.local');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        foreach ($tenants as $tenant) {
            $user->addTenant($tenant);
        }

        return $user;
    }
}
