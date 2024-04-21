<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225181804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample ADD section_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('CREATE INDEX IDX_F10B76C3D823E37A ON sample (section_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C3D823E37A');
        $this->addSql('DROP INDEX IDX_F10B76C3D823E37A ON sample');
        $this->addSql('ALTER TABLE sample DROP section_id');
    }
}
