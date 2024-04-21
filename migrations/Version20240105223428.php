<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240105223428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C3D823E37A');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C3D823E37A');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
