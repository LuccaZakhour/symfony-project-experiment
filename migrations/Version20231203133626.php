<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231203133626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE catalog_item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sku VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_app_setting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, serial_number VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, manufacturer VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE experiment (id INT AUTO_INCREMENT NOT NULL, template_id INT DEFAULT NULL, protocols_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, INDEX IDX_136F58B25DA0FB8 (template_id), INDEX IDX_136F58B2820DB171 (protocols_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE experiment_samples (experiment_id INT NOT NULL, sample_id INT NOT NULL, INDEX IDX_37D190DDFF444C8 (experiment_id), INDEX IDX_37D190DD1B1FEA20 (sample_id), PRIMARY KEY(experiment_id, sample_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE experiment_user (experiment_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_966C17E4FF444C8 (experiment_id), INDEX IDX_966C17E4A76ED395 (user_id), PRIMARY KEY(experiment_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_management_experiment (experiment_source INT NOT NULL, experiment_target INT NOT NULL, INDEX IDX_E3AA2A84A840540E (experiment_source), INDEX IDX_E3AA2A84B1A50481 (experiment_target), PRIMARY KEY(experiment_source, experiment_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_experiment (experiment_id INT NOT NULL, study_id INT NOT NULL, INDEX IDX_94F33CD2FF444C8 (experiment_id), INDEX IDX_94F33CD2E7B003E9 (study_id), PRIMARY KEY(experiment_id, study_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, experiment_id INT DEFAULT NULL, protocol_id INT DEFAULT NULL, sample_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, filesize INT NOT NULL, description VARCHAR(255) NOT NULL, filetype VARCHAR(255) NOT NULL, filepath LONGTEXT DEFAULT NULL, INDEX IDX_8C9F3610FF444C8 (experiment_id), INDEX IDX_8C9F3610CCD59258 (protocol_id), INDEX IDX_8C9F36101B1FEA20 (sample_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, experiment_id INT DEFAULT NULL, sample_id INT DEFAULT NULL, protocol_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, url LONGTEXT NOT NULL, label VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_36AC99F1FF444C8 (experiment_id), INDEX IDX_36AC99F11B1FEA20 (sample_id), INDEX IDX_36AC99F1CCD59258 (protocol_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, department VARCHAR(255) DEFAULT NULL, building VARCHAR(255) DEFAULT NULL, floor VARCHAR(255) DEFAULT NULL, room VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_entity (id INT AUTO_INCREMENT NOT NULL, ordered_by_id INT DEFAULT NULL, order_number VARCHAR(50) NOT NULL, order_date DATETIME NOT NULL, supplier VARCHAR(255) DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, total_amount INT DEFAULT NULL, INDEX IDX_CDA754BD91FF3C4D (ordered_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, group_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, short_name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, notes LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5C93B3A4FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE protocol (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, steps LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE protocol_task (protocol_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_57702452CCD59258 (protocol_id), INDEX IDX_577024528DB60186 (task_id), PRIMARY KEY(protocol_id, task_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, equipment_id INT DEFAULT NULL, user_id INT DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, reservation_code VARCHAR(255) NOT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_42C84955517FE9FE (equipment_id), INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sample (id INT AUTO_INCREMENT NOT NULL, sample_type_id INT DEFAULT NULL, storage_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, barcode VARCHAR(255) DEFAULT NULL, position VARCHAR(10) DEFAULT NULL, INDEX IDX_F10B76C3D5064105 (sample_type_id), INDEX IDX_F10B76C35CC5DB90 (storage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_management_sample (sample_id INT NOT NULL, experiment_id INT NOT NULL, INDEX IDX_2EE12E541B1FEA20 (sample_id), INDEX IDX_2EE12E54FF444C8 (experiment_id), PRIMARY KEY(sample_id, experiment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_sample (sample_id INT NOT NULL, study_id INT NOT NULL, INDEX IDX_515EEEEB1B1FEA20 (sample_id), INDEX IDX_515EEEEBE7B003E9 (study_id), PRIMARY KEY(sample_id, study_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sample_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, template_id INT DEFAULT NULL, experiment_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_2D737AEF5DA0FB8 (template_id), INDEX IDX_2D737AEFFF444C8 (experiment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section_links (section_id INT NOT NULL, link_id INT NOT NULL, INDEX IDX_46D5C67CD823E37A (section_id), INDEX IDX_46D5C67CADA40271 (link_id), PRIMARY KEY(section_id, link_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, storage_type_id INT DEFAULT NULL, location_id INT DEFAULT NULL, experiment_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, dimensions VARCHAR(255) DEFAULT NULL, position_taken JSON DEFAULT NULL, grid_type VARCHAR(255) DEFAULT NULL, INDEX IDX_547A1B34B270BFF1 (storage_type_id), INDEX IDX_547A1B3464D218E (location_id), INDEX IDX_547A1B34FF444C8 (experiment_id), INDEX IDX_547A1B34727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study (id INT AUTO_INCREMENT NOT NULL, lead_researcher_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, INDEX IDX_E67F9749FDB1FB34 (lead_researcher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, contact_email VARCHAR(255) NOT NULL, contact_phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supply_order (id INT AUTO_INCREMENT NOT NULL, ordered_by_id INT DEFAULT NULL, order_number VARCHAR(255) NOT NULL, order_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_91F9D33C91FF3C4D (ordered_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supply_order_item (id INT AUTO_INCREMENT NOT NULL, supply_order_id INT DEFAULT NULL, item_name VARCHAR(255) NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_C041E5F125440531 (supply_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system_capability (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system_setting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, assigned_to_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(50) NOT NULL, due_date DATETIME DEFAULT NULL, INDEX IDX_527EDB25F4BD7827 (assigned_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_experiment (task_id INT NOT NULL, experiment_id INT NOT NULL, INDEX IDX_F7C131D8DB60186 (task_id), INDEX IDX_F7C131DFF444C8 (experiment_id), PRIMARY KEY(task_id, experiment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_sample (task_id INT NOT NULL, sample_id INT NOT NULL, INDEX IDX_CAA2F2108DB60186 (task_id), INDEX IDX_CAA2F2101B1FEA20 (sample_id), PRIMARY KEY(task_id, sample_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_protocol (task_id INT NOT NULL, protocol_id INT NOT NULL, INDEX IDX_880056008DB60186 (task_id), INDEX IDX_88005600CCD59258 (protocol_id), PRIMARY KEY(task_id, protocol_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_management (id INT AUTO_INCREMENT NOT NULL, assigned_to_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, due_date DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_175DDEE0F4BD7827 (assigned_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_zone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, offset VARCHAR(10) NOT NULL, is_default TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, projects_id INT DEFAULT NULL, salutation VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', password VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, removed_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6491EDE0F55 (projects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, role VARCHAR(50) DEFAULT NULL, permissions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE experiment ADD CONSTRAINT FK_136F58B25DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id)');
        $this->addSql('ALTER TABLE experiment ADD CONSTRAINT FK_136F58B2820DB171 FOREIGN KEY (protocols_id) REFERENCES protocol (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE experiment_samples ADD CONSTRAINT FK_37D190DDFF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE experiment_samples ADD CONSTRAINT FK_37D190DD1B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE experiment_user ADD CONSTRAINT FK_966C17E4FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE experiment_user ADD CONSTRAINT FK_966C17E4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_management_experiment ADD CONSTRAINT FK_E3AA2A84A840540E FOREIGN KEY (experiment_source) REFERENCES experiment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_management_experiment ADD CONSTRAINT FK_E3AA2A84B1A50481 FOREIGN KEY (experiment_target) REFERENCES experiment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_experiment ADD CONSTRAINT FK_94F33CD2FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_experiment ADD CONSTRAINT FK_94F33CD2E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36101B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id)');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F1FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id)');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F11B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id)');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F1CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id)');
        $this->addSql('ALTER TABLE order_entity ADD CONSTRAINT FK_CDA754BD91FF3C4D FOREIGN KEY (ordered_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4FE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id)');
        $this->addSql('ALTER TABLE protocol_task ADD CONSTRAINT FK_57702452CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id)');
        $this->addSql('ALTER TABLE protocol_task ADD CONSTRAINT FK_577024528DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3D5064105 FOREIGN KEY (sample_type_id) REFERENCES sample_type (id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C35CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE task_management_sample ADD CONSTRAINT FK_2EE12E541B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_management_sample ADD CONSTRAINT FK_2EE12E54FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_sample ADD CONSTRAINT FK_515EEEEB1B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_sample ADD CONSTRAINT FK_515EEEEBE7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF5DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id)');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEFFF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id)');
        $this->addSql('ALTER TABLE section_links ADD CONSTRAINT FK_46D5C67CD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_links ADD CONSTRAINT FK_46D5C67CADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34B270BFF1 FOREIGN KEY (storage_type_id) REFERENCES storage_type (id)');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B3464D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id)');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34727ACA70 FOREIGN KEY (parent_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F9749FDB1FB34 FOREIGN KEY (lead_researcher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE supply_order ADD CONSTRAINT FK_91F9D33C91FF3C4D FOREIGN KEY (ordered_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE supply_order_item ADD CONSTRAINT FK_C041E5F125440531 FOREIGN KEY (supply_order_id) REFERENCES supply_order (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task_experiment ADD CONSTRAINT FK_F7C131D8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task_experiment ADD CONSTRAINT FK_F7C131DFF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_sample ADD CONSTRAINT FK_CAA2F2108DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_sample ADD CONSTRAINT FK_CAA2F2101B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_protocol ADD CONSTRAINT FK_880056008DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_protocol ADD CONSTRAINT FK_88005600CCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_management ADD CONSTRAINT FK_175DDEE0F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491EDE0F55 FOREIGN KEY (projects_id) REFERENCES projects (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE experiment DROP FOREIGN KEY FK_136F58B25DA0FB8');
        $this->addSql('ALTER TABLE experiment DROP FOREIGN KEY FK_136F58B2820DB171');
        $this->addSql('ALTER TABLE experiment_samples DROP FOREIGN KEY FK_37D190DDFF444C8');
        $this->addSql('ALTER TABLE experiment_samples DROP FOREIGN KEY FK_37D190DD1B1FEA20');
        $this->addSql('ALTER TABLE experiment_user DROP FOREIGN KEY FK_966C17E4FF444C8');
        $this->addSql('ALTER TABLE experiment_user DROP FOREIGN KEY FK_966C17E4A76ED395');
        $this->addSql('ALTER TABLE task_management_experiment DROP FOREIGN KEY FK_E3AA2A84A840540E');
        $this->addSql('ALTER TABLE task_management_experiment DROP FOREIGN KEY FK_E3AA2A84B1A50481');
        $this->addSql('ALTER TABLE study_experiment DROP FOREIGN KEY FK_94F33CD2FF444C8');
        $this->addSql('ALTER TABLE study_experiment DROP FOREIGN KEY FK_94F33CD2E7B003E9');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610FF444C8');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610CCD59258');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36101B1FEA20');
        $this->addSql('ALTER TABLE link DROP FOREIGN KEY FK_36AC99F1FF444C8');
        $this->addSql('ALTER TABLE link DROP FOREIGN KEY FK_36AC99F11B1FEA20');
        $this->addSql('ALTER TABLE link DROP FOREIGN KEY FK_36AC99F1CCD59258');
        $this->addSql('ALTER TABLE order_entity DROP FOREIGN KEY FK_CDA754BD91FF3C4D');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4FE54D947');
        $this->addSql('ALTER TABLE protocol_task DROP FOREIGN KEY FK_57702452CCD59258');
        $this->addSql('ALTER TABLE protocol_task DROP FOREIGN KEY FK_577024528DB60186');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955517FE9FE');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C3D5064105');
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C35CC5DB90');
        $this->addSql('ALTER TABLE task_management_sample DROP FOREIGN KEY FK_2EE12E541B1FEA20');
        $this->addSql('ALTER TABLE task_management_sample DROP FOREIGN KEY FK_2EE12E54FF444C8');
        $this->addSql('ALTER TABLE study_sample DROP FOREIGN KEY FK_515EEEEB1B1FEA20');
        $this->addSql('ALTER TABLE study_sample DROP FOREIGN KEY FK_515EEEEBE7B003E9');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF5DA0FB8');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEFFF444C8');
        $this->addSql('ALTER TABLE section_links DROP FOREIGN KEY FK_46D5C67CD823E37A');
        $this->addSql('ALTER TABLE section_links DROP FOREIGN KEY FK_46D5C67CADA40271');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34B270BFF1');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B3464D218E');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34FF444C8');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34727ACA70');
        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F9749FDB1FB34');
        $this->addSql('ALTER TABLE supply_order DROP FOREIGN KEY FK_91F9D33C91FF3C4D');
        $this->addSql('ALTER TABLE supply_order_item DROP FOREIGN KEY FK_C041E5F125440531');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25F4BD7827');
        $this->addSql('ALTER TABLE task_experiment DROP FOREIGN KEY FK_F7C131D8DB60186');
        $this->addSql('ALTER TABLE task_experiment DROP FOREIGN KEY FK_F7C131DFF444C8');
        $this->addSql('ALTER TABLE task_sample DROP FOREIGN KEY FK_CAA2F2108DB60186');
        $this->addSql('ALTER TABLE task_sample DROP FOREIGN KEY FK_CAA2F2101B1FEA20');
        $this->addSql('ALTER TABLE task_protocol DROP FOREIGN KEY FK_880056008DB60186');
        $this->addSql('ALTER TABLE task_protocol DROP FOREIGN KEY FK_88005600CCD59258');
        $this->addSql('ALTER TABLE task_management DROP FOREIGN KEY FK_175DDEE0F4BD7827');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491EDE0F55');
        $this->addSql('DROP TABLE catalog_item');
        $this->addSql('DROP TABLE client_app_setting');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE experiment');
        $this->addSql('DROP TABLE experiment_samples');
        $this->addSql('DROP TABLE experiment_user');
        $this->addSql('DROP TABLE task_management_experiment');
        $this->addSql('DROP TABLE study_experiment');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE order_entity');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE protocol');
        $this->addSql('DROP TABLE protocol_task');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE sample');
        $this->addSql('DROP TABLE task_management_sample');
        $this->addSql('DROP TABLE study_sample');
        $this->addSql('DROP TABLE sample_type');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE section_links');
        $this->addSql('DROP TABLE storage');
        $this->addSql('DROP TABLE storage_type');
        $this->addSql('DROP TABLE study');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE supply_order');
        $this->addSql('DROP TABLE supply_order_item');
        $this->addSql('DROP TABLE system_capability');
        $this->addSql('DROP TABLE system_setting');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_experiment');
        $this->addSql('DROP TABLE task_sample');
        $this->addSql('DROP TABLE task_protocol');
        $this->addSql('DROP TABLE task_management');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP TABLE time_zone');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
