<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260528180355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create blog_categories and blog_posts tables (Phase 1)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_categories (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(100) NOT NULL, label_bg VARCHAR(255) NOT NULL, label_en VARCHAR(255) NOT NULL, sort_order INT DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX UNIQ_DC356481989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_posts (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, slug VARCHAR(100) NOT NULL, title_bg VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, excerpt_bg LONGTEXT DEFAULT NULL, excerpt_en LONGTEXT DEFAULT NULL, content_bg LONGTEXT DEFAULT NULL, content_en LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_78B2F932989D9B62 (slug), INDEX IDX_78B2F93212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_posts ADD CONSTRAINT FK_78B2F93212469DE2 FOREIGN KEY (category_id) REFERENCES blog_categories (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_posts DROP FOREIGN KEY FK_78B2F93212469DE2');
        $this->addSql('DROP TABLE blog_categories');
        $this->addSql('DROP TABLE blog_posts');
    }
}
