<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231210164107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study_experiment DROP FOREIGN KEY FK_94F33CD2E7B003E9');
        $this->addSql('ALTER TABLE study_experiment DROP FOREIGN KEY FK_94F33CD2FF444C8');
        $this->addSql('DROP TABLE study_experiment');
        $this->addSql('ALTER TABLE experiment ADD study_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE experiment ADD CONSTRAINT FK_136F58B2E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('CREATE INDEX IDX_136F58B2E7B003E9 ON experiment (study_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE study_experiment (experiment_id INT NOT NULL, study_id INT NOT NULL, INDEX IDX_94F33CD2E7B003E9 (study_id), INDEX IDX_94F33CD2FF444C8 (experiment_id), PRIMARY KEY(experiment_id, study_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE study_experiment ADD CONSTRAINT FK_94F33CD2E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_experiment ADD CONSTRAINT FK_94F33CD2FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE experiment DROP FOREIGN KEY FK_136F58B2E7B003E9');
        $this->addSql('DROP INDEX IDX_136F58B2E7B003E9 ON experiment');
        $this->addSql('ALTER TABLE experiment DROP study_id');
    }
}
