<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211175133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE custom_field (id INT AUTO_INCREMENT NOT NULL, sample_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, configuration JSON DEFAULT NULL, INDEX IDX_98F8BD31D5064105 (sample_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE custom_field ADD CONSTRAINT FK_98F8BD31D5064105 FOREIGN KEY (sample_type_id) REFERENCES sample_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE custom_field DROP FOREIGN KEY FK_98F8BD31D5064105');
        $this->addSql('DROP TABLE custom_field');
    }
}
