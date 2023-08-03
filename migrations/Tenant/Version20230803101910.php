<?php

declare(strict_types=1);

namespace DoctrineMigrations\Tenant;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230803101910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove tenant relation from blog post';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post DROP tenant_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE blog_post ADD tenant_id INT NOT NULL');
    }
}
