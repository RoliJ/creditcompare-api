<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311231557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sorting_criteria (
          id INT AUTO_INCREMENT NOT NULL,
          table_name VARCHAR(100) NOT NULL COMMENT \'Table name where the field resides, e.g. \'\'credit_card_features\'\'\',
          field_name VARCHAR(100) NOT NULL COMMENT \'Field name to sort by, e.g. \'\'annualFees\'\'\',
          direction VARCHAR(4) DEFAULT \'ASC\' NOT NULL COMMENT \'Sort direction: ASC or DESC\',
          priority INT NOT NULL COMMENT \'Sorting priority, 1 for highest priority\',
          created_at DATETIME NOT NULL,
          updated_at DATETIME NOT NULL,
          deleted_at DATETIME DEFAULT NULL,
          UNIQUE INDEX unique_field_priority (field_name, priority),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sorting_criteria');
    }
}
