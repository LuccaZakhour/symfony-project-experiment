<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231224171148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE protocol ADD user_id INT DEFAULT NULL, ADD prot_id INT NOT NULL, ADD meta JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE protocol ADD CONSTRAINT FK_C8C0BC4CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C8C0BC4CA76ED395 ON protocol (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE protocol DROP FOREIGN KEY FK_C8C0BC4CA76ED395');
        $this->addSql('DROP INDEX IDX_C8C0BC4CA76ED395 ON protocol');
        $this->addSql('ALTER TABLE protocol DROP user_id, DROP prot_id, DROP meta');
    }
}
