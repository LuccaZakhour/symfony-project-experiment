<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231228135213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample ADD user_id INT DEFAULT NULL, ADD parent_id INT DEFAULT NULL, ADD meta JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3727ACA70 FOREIGN KEY (parent_id) REFERENCES sample (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F10B76C3A76ED395 ON sample (user_id)');
        $this->addSql('CREATE INDEX IDX_F10B76C3727ACA70 ON sample (parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C3A76ED395');
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C3727ACA70');
        $this->addSql('DROP INDEX IDX_F10B76C3A76ED395 ON sample');
        $this->addSql('DROP INDEX IDX_F10B76C3727ACA70 ON sample');
        $this->addSql('ALTER TABLE sample DROP user_id, DROP parent_id, DROP meta');
    }
}
