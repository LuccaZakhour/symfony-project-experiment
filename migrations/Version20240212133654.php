<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240212133654 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE experiment ADD signed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD signedBy INT DEFAULT NULL');
        $this->addSql('ALTER TABLE experiment ADD CONSTRAINT FK_136F58B2C2903630 FOREIGN KEY (signedBy) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_136F58B2C2903630 ON experiment (signedBy)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE experiment DROP FOREIGN KEY FK_136F58B2C2903630');
        $this->addSql('DROP INDEX IDX_136F58B2C2903630 ON experiment');
        $this->addSql('ALTER TABLE experiment DROP signed_at, DROP signedBy');
    }
}
