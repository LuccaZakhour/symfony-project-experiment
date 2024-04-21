<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231227173449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE storage_layer_id storage_layer_id VARCHAR(255) DEFAULT NULL, CHANGE barcode barcode VARCHAR(255) DEFAULT NULL, CHANGE meta meta JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage CHANGE storage_layer_id storage_layer_id VARCHAR(255) NOT NULL, CHANGE barcode barcode VARCHAR(255) NOT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE meta meta JSON NOT NULL');
    }
}
