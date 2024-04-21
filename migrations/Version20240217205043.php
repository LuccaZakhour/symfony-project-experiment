<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217205043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610CCD59258');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610CCD59258');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
