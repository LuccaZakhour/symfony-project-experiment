<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231221160844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE experiment DROP FOREIGN KEY FK_136F58B25DA0FB8');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF5DA0FB8');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP INDEX IDX_136F58B25DA0FB8 ON experiment');
        $this->addSql('ALTER TABLE experiment CHANGE template_id protocol_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE experiment ADD CONSTRAINT FK_136F58B2CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id)');
        $this->addSql('CREATE INDEX IDX_136F58B2CCD59258 ON experiment (protocol_id)');
        $this->addSql('DROP INDEX IDX_2D737AEF5DA0FB8 ON section');
        $this->addSql('ALTER TABLE section DROP template_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE experiment DROP FOREIGN KEY FK_136F58B2CCD59258');
        $this->addSql('DROP INDEX IDX_136F58B2CCD59258 ON experiment');
        $this->addSql('ALTER TABLE experiment CHANGE protocol_id template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE experiment ADD CONSTRAINT FK_136F58B25DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_136F58B25DA0FB8 ON experiment (template_id)');
        $this->addSql('ALTER TABLE section ADD template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF5DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2D737AEF5DA0FB8 ON section (template_id)');
    }
}
