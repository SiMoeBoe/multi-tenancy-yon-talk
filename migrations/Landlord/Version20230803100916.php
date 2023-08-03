<?php

declare(strict_types=1);

namespace DoctrineMigrations\Landlord;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230803100916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add database to tenant table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE tenant ADD database VARCHAR(255) NOT NULL DEFAULT ''");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tenant DROP database');
    }
}
