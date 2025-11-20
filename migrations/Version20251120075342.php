<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120075342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(100) NOT NULL, name_bg VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_3AF34668989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, slug VARCHAR(100) NOT NULL, name_bg VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, short_description_bg LONGTEXT DEFAULT NULL, short_description_en LONGTEXT DEFAULT NULL, description_bg LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, features_bg JSON DEFAULT NULL, features_en JSON DEFAULT NULL, suitable_for_bg LONGTEXT DEFAULT NULL, suitable_for_en LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, quantity INT DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_B3BA5A5A989D9B62 (slug), INDEX IDX_B3BA5A5A12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE contact_request CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sample ADD product_id INT DEFAULT NULL, DROP sample_type');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A12469DE2');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE products');
        $this->addSql('ALTER TABLE contact_request CHANGE product_id product_id VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE sample ADD sample_type VARCHAR(255) DEFAULT NULL, DROP product_id');
    }
}
