<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250310234652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_logs (id INT AUTO_INCREMENT NOT NULL, request_url LONGTEXT NOT NULL, response LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banks (id INT AUTO_INCREMENT NOT NULL, bank_id INT NOT NULL COMMENT \'bankid: Unique ID of the bank\', name VARCHAR(255) NOT NULL COMMENT \'bank: Name of the bank\', description LONGTEXT DEFAULT NULL COMMENT \'Extra description about the bank\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_AB06379611C8FB41 (bank_id), UNIQUE INDEX UNIQ_AB0637965E237E06 (name), INDEX idx_bank_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card_types (id INT AUTO_INCREMENT NOT NULL, name ENUM(\'credit\', \'debit\') NOT NULL COMMENT \'cardtype_text: Credit or Debit card\', card_type SMALLINT DEFAULT NULL COMMENT \'cardtype: Card type ID; 0 = credit, 2 = debit\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX idx_card_type_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit_card_edits (id INT AUTO_INCREMENT NOT NULL, edited_by_id INT NOT NULL, credit_card_id INT NOT NULL, table_name VARCHAR(50) NOT NULL, column_name VARCHAR(50) NOT NULL, old_value LONGTEXT DEFAULT NULL, new_value LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_2DA149D7DD7B2EBC (edited_by_id), INDEX idx_edit_credit_card (credit_card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit_card_features (id INT AUTO_INCREMENT NOT NULL, credit_card_id INT NOT NULL, link VARCHAR(255) NOT NULL COMMENT \'link: Deeplink\', test_seal VARCHAR(255) DEFAULT NULL COMMENT \'testsiegel: URL to the test seal in a standard format\', test_seal_url VARCHAR(255) DEFAULT NULL COMMENT \'testsiegel_url: URL to the test seal in a standard format\', notes LONGTEXT NOT NULL COMMENT \'anmerkungen: Extra information\', rating NUMERIC(2, 1) DEFAULT NULL COMMENT \'bewertung: Star rating from the provider (1-5)\', has_evaluation TINYINT(1) DEFAULT 0 COMMENT \'bewertung_anzahl: Evaluation number; 0 = No, 1 = Yes\', incentive DOUBLE PRECISION NOT NULL COMMENT \'incentive: T.A.E.\', annual_fees DOUBLE PRECISION NOT NULL COMMENT \'gebuehren: Annual charges of the credit card in Euro (card fees)\', annual_transaction_costs DOUBLE PRECISION NOT NULL COMMENT \'kosten: Annual transaction costs of the credit card in Euro\', has_bonus_program TINYINT(1) DEFAULT 0 COMMENT \'bonusprogram: If the credit card offers a bonus program; 0 = No, 1 = Yes\', has_additional_insurance TINYINT(1) DEFAULT 0 COMMENT \'insurances: Offers additional insurance cover; 0 = No, 1 = Yes\', has_discount_benefits TINYINT(1) DEFAULT 0 COMMENT \'benefits: Offers discount benefits on selected Partners; 0 = No, 1 = Yes\', has_additional_services TINYINT(1) DEFAULT 0 COMMENT \'services: Offers additional services?; 0 = No, 1 = Yes\', special_features VARCHAR(255) DEFAULT NULL COMMENT \'besonderheiten: Text field for special features of the credit card\', participation_fee VARCHAR(255) DEFAULT NULL COMMENT \'gebuehrenmitaktion: Participation fee\', participation_cost VARCHAR(255) DEFAULT NULL COMMENT \'kostenmitaktion: Participation cost\', first_year_fee DOUBLE PRECISION NOT NULL COMMENT \'gebuehrenjahr1: 1st year fee\', second_year_fee DOUBLE PRECISION NOT NULL COMMENT \'dauerhaft: Fee from 2nd year\', gc_domestic_atm_fee DOUBLE PRECISION DEFAULT NULL COMMENT \'gc_atmfree_domestic: National ATM fee\', gc_international_atm_fee DOUBLE PRECISION DEFAULT NULL COMMENT \'gc_atmfree_international: International ATM fee\', cc_domestic_atm_fee DOUBLE PRECISION DEFAULT NULL COMMENT \'cc_atmfree_domestic: Offers a free fee on national ATM?\', cc_international_atm_fee DOUBLE PRECISION DEFAULT NULL COMMENT \'cc_atmfree_international: Offers a free fee on international ATM?\', incentive_amount DOUBLE PRECISION NOT NULL COMMENT \'incentive_amount: T.A.E.\', interest_rate DOUBLE PRECISION DEFAULT NULL COMMENT \'habenzins: Interest rate\', shall_interest_rate DOUBLE PRECISION DEFAULT NULL COMMENT \'sollzins: Shall interest rate\', cc_euro_atm_fee DOUBLE PRECISION DEFAULT NULL COMMENT \'cc_atmfree_euro: Offers a free fee on EU ATM?\', kk_offer TINYINT(1) DEFAULT 0 COMMENT \'kkoffer; 0 = No, 1 = Yes\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX idx_feature_credit_card (credit_card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit_card_images (id INT AUTO_INCREMENT NOT NULL, credit_card_id INT NOT NULL, image_url VARCHAR(255) NOT NULL COMMENT \'logo: Credit card logo (120x76)\', local_path VARCHAR(255) DEFAULT NULL COMMENT \'Local storage path if the image is downloaded\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX idx_credit_card_image (credit_card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit_cards (id INT AUTO_INCREMENT NOT NULL, card_type_id INT NOT NULL, bank_id INT NOT NULL, product_id INT NOT NULL COMMENT \'product id & productid: Unique ID of the product\', name VARCHAR(255) NOT NULL COMMENT \'produkt: Product title\', description LONGTEXT DEFAULT NULL COMMENT \'anmerkungen: Extra information\', sort INT DEFAULT NULL, has_admin_edition TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5CADD6534584665A (product_id), INDEX idx_credit_card_product (product_id), INDEX idx_credit_card_bank (bank_id), INDEX idx_credit_card_type (card_type_id), INDEX idx_credit_card_sort (sort), INDEX idx_credit_card_has_admin_edition (has_admin_edition), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currencies (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(3) NOT NULL, name VARCHAR(50) NOT NULL, symbol VARCHAR(10) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_37C4469377153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE editables (id INT AUTO_INCREMENT NOT NULL, table_name VARCHAR(50) NOT NULL, field_name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_2DEDCC6F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permission (role_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_6F7DF886D60322AC (role_id), INDEX IDX_6F7DF886FED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_permission (user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_472E5446A76ED395 (user_id), INDEX IDX_472E5446FED90CCA (permission_id), PRIMARY KEY(user_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credit_card_edits ADD CONSTRAINT FK_2DA149D7DD7B2EBC FOREIGN KEY (edited_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE credit_card_edits ADD CONSTRAINT FK_2DA149D77048FD0F FOREIGN KEY (credit_card_id) REFERENCES credit_cards (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE credit_card_features ADD CONSTRAINT FK_90DE4F047048FD0F FOREIGN KEY (credit_card_id) REFERENCES credit_cards (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE credit_card_images ADD CONSTRAINT FK_BE0A0B2E7048FD0F FOREIGN KEY (credit_card_id) REFERENCES credit_cards (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE credit_cards ADD CONSTRAINT FK_5CADD653925606E5 FOREIGN KEY (card_type_id) REFERENCES card_types (id)');
        $this->addSql('ALTER TABLE credit_cards ADD CONSTRAINT FK_5CADD65311C8FB41 FOREIGN KEY (bank_id) REFERENCES banks (id)');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE credit_card_edits DROP FOREIGN KEY FK_2DA149D7DD7B2EBC');
        $this->addSql('ALTER TABLE credit_card_edits DROP FOREIGN KEY FK_2DA149D77048FD0F');
        $this->addSql('ALTER TABLE credit_card_features DROP FOREIGN KEY FK_90DE4F047048FD0F');
        $this->addSql('ALTER TABLE credit_card_images DROP FOREIGN KEY FK_BE0A0B2E7048FD0F');
        $this->addSql('ALTER TABLE credit_cards DROP FOREIGN KEY FK_5CADD653925606E5');
        $this->addSql('ALTER TABLE credit_cards DROP FOREIGN KEY FK_5CADD65311C8FB41');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886D60322AC');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886FED90CCA');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446A76ED395');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446FED90CCA');
        $this->addSql('DROP TABLE api_logs');
        $this->addSql('DROP TABLE banks');
        $this->addSql('DROP TABLE card_types');
        $this->addSql('DROP TABLE credit_card_edits');
        $this->addSql('DROP TABLE credit_card_features');
        $this->addSql('DROP TABLE credit_card_images');
        $this->addSql('DROP TABLE credit_cards');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE editables');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE role_permission');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_permission');
    }
}
