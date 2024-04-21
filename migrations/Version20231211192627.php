<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231211192627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE experiment DROP FOREIGN KEY FK_136F58B2CCD59258');
        $this->addSql('DROP INDEX IDX_136F58B2CCD59258 ON experiment');
        $this->addSql('ALTER TABLE experiment DROP protocol_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE experiment ADD protocol_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE experiment ADD CONSTRAINT FK_136F58B2CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_136F58B2CCD59258 ON experiment (protocol_id)');
    }
}
