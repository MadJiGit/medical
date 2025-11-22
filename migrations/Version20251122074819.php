<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to create Manufacturer entity and migrate data from products.manufacturer
 */
final class Version20251122074819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create manufacturers table and migrate data from products.manufacturer column';
    }

    public function up(Schema $schema): void
    {
        // 1. Create manufacturers table
        $this->addSql('CREATE TABLE manufacturers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(100) NOT NULL, website VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_94565B12989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 2. Migrate existing manufacturers from products table
        $this->addSql('INSERT INTO manufacturers (name, slug, created_at) SELECT DISTINCT manufacturer, LOWER(REPLACE(REPLACE(manufacturer, " ", "-"), ".", "")), NOW() FROM products WHERE manufacturer IS NOT NULL AND manufacturer != ""');

        // 3. Add manufacturer_entity_id column to products
        $this->addSql('ALTER TABLE products ADD manufacturer_entity_id INT DEFAULT NULL');

        // 4. Update products with manufacturer_entity_id based on manufacturer name
        $this->addSql('UPDATE products p SET manufacturer_entity_id = (SELECT m.id FROM manufacturers m WHERE m.name = p.manufacturer) WHERE p.manufacturer IS NOT NULL AND p.manufacturer != ""');

        // 5. Add foreign key constraint
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A98CC93BC FOREIGN KEY (manufacturer_entity_id) REFERENCES manufacturers (id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A98CC93BC ON products (manufacturer_entity_id)');

        // 6. Drop old manufacturer column
        $this->addSql('ALTER TABLE products DROP manufacturer');
    }

    public function down(Schema $schema): void
    {
        // 1. Add back manufacturer column
        $this->addSql('ALTER TABLE products ADD manufacturer VARCHAR(255) DEFAULT NULL');

        // 2. Migrate data back
        $this->addSql('UPDATE products p SET manufacturer = (SELECT m.name FROM manufacturers m WHERE m.id = p.manufacturer_entity_id) WHERE p.manufacturer_entity_id IS NOT NULL');

        // 3. Drop foreign key and column
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A98CC93BC');
        $this->addSql('DROP INDEX IDX_B3BA5A5A98CC93BC ON products');
        $this->addSql('ALTER TABLE products DROP manufacturer_entity_id');

        // 4. Drop manufacturers table
        $this->addSql('DROP TABLE manufacturers');
    }
}
