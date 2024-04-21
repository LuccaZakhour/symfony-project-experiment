<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231228192027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B3464D218E');
        $this->addSql('DROP TABLE location');
        $this->addSql('ALTER TABLE equipment DROP location');
        $this->addSql('DROP INDEX IDX_547A1B3464D218E ON storage');
        $this->addSql('ALTER TABLE storage DROP location_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, department VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, building VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, floor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, room VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE equipment ADD location VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE storage ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B3464D218E FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_547A1B3464D218E ON storage (location_id)');
    }
}
