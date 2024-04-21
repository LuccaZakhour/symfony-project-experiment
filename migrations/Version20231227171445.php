<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231227171445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage ADD user_id INT DEFAULT NULL, ADD storage_layer_id VARCHAR(255) NOT NULL, ADD barcode VARCHAR(255) NOT NULL, ADD meta JSON NOT NULL, ADD department VARCHAR(255) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD building VARCHAR(255) DEFAULT NULL, ADD floor VARCHAR(255) DEFAULT NULL, ADD room VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_547A1B34A76ED395 ON storage (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34A76ED395');
        $this->addSql('DROP INDEX IDX_547A1B34A76ED395 ON storage');
        $this->addSql('ALTER TABLE storage DROP user_id, DROP storage_layer_id, DROP barcode, DROP meta, DROP department, DROP address, DROP building, DROP floor, DROP room');
    }
}
