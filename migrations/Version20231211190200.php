<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231211190200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE experiment ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE experiment ADD CONSTRAINT FK_136F58B2166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('CREATE INDEX IDX_136F58B2166D1F9C ON experiment (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE experiment DROP FOREIGN KEY FK_136F58B2166D1F9C');
        $this->addSql('DROP INDEX IDX_136F58B2166D1F9C ON experiment');
        $this->addSql('ALTER TABLE experiment DROP project_id');
    }
}
