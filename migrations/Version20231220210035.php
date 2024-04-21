<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220210035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE protocol_field (id INT AUTO_INCREMENT NOT NULL, protocol_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, INDEX IDX_9591AF38CCD59258 (protocol_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE protocol_field ADD CONSTRAINT FK_9591AF38CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id)');
        $this->addSql('ALTER TABLE protocol_task DROP FOREIGN KEY FK_57702452CCD59258');
        $this->addSql('ALTER TABLE protocol_task DROP FOREIGN KEY FK_577024528DB60186');
        $this->addSql('DROP TABLE protocol_task');
        $this->addSql('ALTER TABLE protocol DROP steps');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE protocol_task (protocol_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_577024528DB60186 (task_id), INDEX IDX_57702452CCD59258 (protocol_id), PRIMARY KEY(protocol_id, task_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE protocol_task ADD CONSTRAINT FK_57702452CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE protocol_task ADD CONSTRAINT FK_577024528DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE protocol_field DROP FOREIGN KEY FK_9591AF38CCD59258');
        $this->addSql('DROP TABLE protocol_field');
        $this->addSql('ALTER TABLE protocol ADD steps LONGTEXT DEFAULT NULL');
    }
}
