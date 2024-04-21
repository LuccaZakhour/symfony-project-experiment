<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227132426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE experiment_samples DROP FOREIGN KEY FK_37D190DD1B1FEA20');
        $this->addSql('ALTER TABLE experiment_samples DROP FOREIGN KEY FK_37D190DDFF444C8');
        $this->addSql('ALTER TABLE task_management_experiment DROP FOREIGN KEY FK_E3AA2A84A840540E');
        $this->addSql('ALTER TABLE task_management_experiment DROP FOREIGN KEY FK_E3AA2A84B1A50481');
        $this->addSql('ALTER TABLE task_management_sample DROP FOREIGN KEY FK_2EE12E541B1FEA20');
        $this->addSql('ALTER TABLE task_management_sample DROP FOREIGN KEY FK_2EE12E54FF444C8');
        $this->addSql('DROP TABLE experiment_samples');
        $this->addSql('DROP TABLE task_management_experiment');
        $this->addSql('DROP TABLE task_management_sample');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE experiment_samples (experiment_id INT NOT NULL, sample_id INT NOT NULL, INDEX IDX_37D190DD1B1FEA20 (sample_id), INDEX IDX_37D190DDFF444C8 (experiment_id), PRIMARY KEY(experiment_id, sample_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE task_management_experiment (experiment_source INT NOT NULL, experiment_target INT NOT NULL, INDEX IDX_E3AA2A84A840540E (experiment_source), INDEX IDX_E3AA2A84B1A50481 (experiment_target), PRIMARY KEY(experiment_source, experiment_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE task_management_sample (sample_id INT NOT NULL, experiment_id INT NOT NULL, INDEX IDX_2EE12E541B1FEA20 (sample_id), INDEX IDX_2EE12E54FF444C8 (experiment_id), PRIMARY KEY(sample_id, experiment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE experiment_samples ADD CONSTRAINT FK_37D190DD1B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE experiment_samples ADD CONSTRAINT FK_37D190DDFF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_management_experiment ADD CONSTRAINT FK_E3AA2A84A840540E FOREIGN KEY (experiment_source) REFERENCES experiment (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_management_experiment ADD CONSTRAINT FK_E3AA2A84B1A50481 FOREIGN KEY (experiment_target) REFERENCES experiment (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_management_sample ADD CONSTRAINT FK_2EE12E541B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_management_sample ADD CONSTRAINT FK_2EE12E54FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
