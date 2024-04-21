<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204145212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study ADD project_id INT DEFAULT NULL, ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F9749166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('CREATE INDEX IDX_E67F9749166D1F9C ON study (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F9749166D1F9C');
        $this->addSql('DROP INDEX IDX_E67F9749166D1F9C ON study');
        $this->addSql('ALTER TABLE study DROP project_id, DROP status');
    }
}
