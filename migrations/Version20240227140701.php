<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227140701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        try {
            $this->connection->executeStatement(
                "UPDATE catalog_item ci LEFT JOIN supplier s ON ci.supplier_id = s.id
                 SET ci.supplier_id = NULL WHERE s.id IS NULL"
            );
            // Check if column exists and add it if it doesn't (MySQL specific)
            $this->addSql("SET @dbname = DATABASE();");
            $this->addSql("SET @tablename = 'catalog_item';");
            $this->addSql("SET @columnname = 'supplier_id';");
            $this->addSql("SET @preparedStatement = (SELECT IF(
                (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
                'SELECT 1', -- Column exists, do nothing
                'ALTER TABLE catalog_item MODIFY supplier_id INT DEFAULT NULL' -- Column doesn't exist, add it
                ));");
            $this->addSql("PREPARE alterTable FROM @preparedStatement;");
            $this->addSql("EXECUTE alterTable;");
            $this->addSql("DEALLOCATE PREPARE alterTable;");
        } catch (\Exception $e) {
            $this->skipIf(true, 'Column "supplier_id" already exists, skipping.');
        }

        // Attempt to make supplier_id nullable if it does not exist. This SQL might need to be adjusted based on your DBMS.
        $this->addSql("ALTER TABLE catalog_item MODIFY supplier_id INT DEFAULT NULL");

        // Then, attempt to add the foreign key constraint.
        $this->addSql('ALTER TABLE catalog_item ADD CONSTRAINT FK_6AAE95682ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('CREATE INDEX IDX_6AAE95682ADD6D8C ON catalog_item (supplier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog_item DROP FOREIGN KEY FK_6AAE95682ADD6D8C');
        $this->addSql('DROP INDEX IDX_6AAE95682ADD6D8C ON catalog_item');
        $this->addSql('ALTER TABLE catalog_item DROP supplier_id');
    }
}
