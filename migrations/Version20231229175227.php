<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229175227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample_series ADD user_id INT DEFAULT NULL, CHANGE series_name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sample_series ADD CONSTRAINT FK_AAA7CDB7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AAA7CDB7A76ED395 ON sample_series (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample_series DROP FOREIGN KEY FK_AAA7CDB7A76ED395');
        $this->addSql('DROP INDEX IDX_AAA7CDB7A76ED395 ON sample_series');
        $this->addSql('ALTER TABLE sample_series DROP user_id, CHANGE name series_name VARCHAR(255) DEFAULT NULL');
    }
}
