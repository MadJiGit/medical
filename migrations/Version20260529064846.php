<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529064846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE specialists (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, specialty_bg VARCHAR(255) DEFAULT NULL, specialty_en VARCHAR(255) DEFAULT NULL, bio_bg LONGTEXT DEFAULT NULL, bio_en LONGTEXT DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, sort_order INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_A11CDE44989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_category_translations RENAME INDEX idx_blog_cat_trans_cat TO IDX_85D2E1FE12469DE2');
        $this->addSql('ALTER TABLE blog_post_translations RENAME INDEX idx_blog_post_trans_post TO IDX_2497E3324B89032C');
        $this->addSql('ALTER TABLE video_category_translations RENAME INDEX idx_video_cat_trans_cat TO IDX_4CD9E63612469DE2');
        $this->addSql('ALTER TABLE video_translations RENAME INDEX idx_video_trans_video TO IDX_6744CB4829C1004E');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE specialists');
        $this->addSql('ALTER TABLE blog_category_translations RENAME INDEX idx_85d2e1fe12469de2 TO IDX_blog_cat_trans_cat');
        $this->addSql('ALTER TABLE blog_post_translations RENAME INDEX idx_2497e3324b89032c TO IDX_blog_post_trans_post');
        $this->addSql('ALTER TABLE video_category_translations RENAME INDEX idx_4cd9e63612469de2 TO IDX_video_cat_trans_cat');
        $this->addSql('ALTER TABLE video_translations RENAME INDEX idx_6744cb4829c1004e TO IDX_video_trans_video');
    }
}
