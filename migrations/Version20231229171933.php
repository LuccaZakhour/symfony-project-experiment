<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229171933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample ADD sample_series_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C390B6B247 FOREIGN KEY (sample_series_id) REFERENCES sample_series (id)');
        $this->addSql('CREATE INDEX IDX_F10B76C390B6B247 ON sample (sample_series_id)');
        $this->addSql('ALTER TABLE sample_series ADD created_at DATETIME NOT NULL, ADD meta JSON DEFAULT NULL, CHANGE description barcode VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C390B6B247');
        $this->addSql('DROP INDEX IDX_F10B76C390B6B247 ON sample');
        $this->addSql('ALTER TABLE sample DROP sample_series_id');
        $this->addSql('ALTER TABLE sample_series DROP created_at, DROP meta, CHANGE barcode description VARCHAR(255) DEFAULT NULL');
    }
}
