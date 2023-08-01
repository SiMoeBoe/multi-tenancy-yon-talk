<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230801204605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user-tenant relation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_tenant (user_id INT NOT NULL, tenant_id INT NOT NULL, PRIMARY KEY(user_id, tenant_id))');
        $this->addSql('CREATE INDEX IDX_2B0BDF5FA76ED395 ON user_tenant (user_id)');
        $this->addSql('CREATE INDEX IDX_2B0BDF5F9033212A ON user_tenant (tenant_id)');
        $this->addSql('ALTER TABLE user_tenant ADD CONSTRAINT FK_2B0BDF5FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_tenant ADD CONSTRAINT FK_2B0BDF5F9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_tenant DROP CONSTRAINT FK_2B0BDF5FA76ED395');
        $this->addSql('ALTER TABLE user_tenant DROP CONSTRAINT FK_2B0BDF5F9033212A');
        $this->addSql('DROP TABLE user_tenant');
    }
}
