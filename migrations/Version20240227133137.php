<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227133137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE link DROP FOREIGN KEY FK_36AC99F11B1FEA20');
        $this->addSql('ALTER TABLE link DROP FOREIGN KEY FK_36AC99F1CCD59258');
        $this->addSql('ALTER TABLE link DROP FOREIGN KEY FK_36AC99F1FF444C8');
        $this->addSql('ALTER TABLE section_links DROP FOREIGN KEY FK_46D5C67CADA40271');
        $this->addSql('ALTER TABLE section_links DROP FOREIGN KEY FK_46D5C67CD823E37A');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE section_links');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, experiment_id INT DEFAULT NULL, sample_id INT DEFAULT NULL, protocol_id INT DEFAULT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_36AC99F11B1FEA20 (sample_id), INDEX IDX_36AC99F1CCD59258 (protocol_id), INDEX IDX_36AC99F1FF444C8 (experiment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE section_links (section_id INT NOT NULL, link_id INT NOT NULL, INDEX IDX_46D5C67CADA40271 (link_id), INDEX IDX_46D5C67CD823E37A (section_id), PRIMARY KEY(section_id, link_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F11B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F1CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F1FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE section_links ADD CONSTRAINT FK_46D5C67CADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_links ADD CONSTRAINT FK_46D5C67CD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
