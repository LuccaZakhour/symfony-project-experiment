<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204181519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34727ACA70');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34727ACA70 FOREIGN KEY (parent_id) REFERENCES storage (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34727ACA70');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34727ACA70 FOREIGN KEY (parent_id) REFERENCES storage (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
