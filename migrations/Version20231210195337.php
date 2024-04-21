<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231210195337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sample_series (id INT AUTO_INCREMENT NOT NULL, series_name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE storage ADD sample_series_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B3490B6B247 FOREIGN KEY (sample_series_id) REFERENCES sample_series (id)');
        $this->addSql('CREATE INDEX IDX_547A1B3490B6B247 ON storage (sample_series_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B3490B6B247');
        $this->addSql('DROP TABLE sample_series');
        $this->addSql('DROP INDEX IDX_547A1B3490B6B247 ON storage');
        $this->addSql('ALTER TABLE storage DROP sample_series_id');
    }
}
