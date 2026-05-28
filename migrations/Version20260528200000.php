<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260528200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Refactor to translation tables for multilingual support (BlogCategory, BlogPost, VideoCategory, Video)';
    }

    public function up(Schema $schema): void
    {
        // Create translation tables
        $this->addSql('CREATE TABLE blog_category_translations (
            id INT AUTO_INCREMENT NOT NULL,
            category_id INT NOT NULL,
            locale VARCHAR(10) NOT NULL,
            label VARCHAR(255) NOT NULL,
            INDEX IDX_blog_cat_trans_cat (category_id),
            UNIQUE INDEX uniq_blog_cat_trans_locale (category_id, locale),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE blog_post_translations (
            id INT AUTO_INCREMENT NOT NULL,
            post_id INT NOT NULL,
            locale VARCHAR(10) NOT NULL,
            title VARCHAR(255) NOT NULL,
            excerpt LONGTEXT DEFAULT NULL,
            content LONGTEXT DEFAULT NULL,
            INDEX IDX_blog_post_trans_post (post_id),
            UNIQUE INDEX uniq_blog_post_trans_locale (post_id, locale),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE video_category_translations (
            id INT AUTO_INCREMENT NOT NULL,
            category_id INT NOT NULL,
            locale VARCHAR(10) NOT NULL,
            label VARCHAR(255) NOT NULL,
            INDEX IDX_video_cat_trans_cat (category_id),
            UNIQUE INDEX uniq_video_cat_trans_locale (category_id, locale),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE video_translations (
            id INT AUTO_INCREMENT NOT NULL,
            video_id INT NOT NULL,
            locale VARCHAR(10) NOT NULL,
            title VARCHAR(255) NOT NULL,
            INDEX IDX_video_trans_video (video_id),
            UNIQUE INDEX uniq_video_trans_locale (video_id, locale),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add FK constraints
        $this->addSql('ALTER TABLE blog_category_translations ADD CONSTRAINT FK_blog_cat_trans_cat FOREIGN KEY (category_id) REFERENCES blog_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post_translations ADD CONSTRAINT FK_blog_post_trans_post FOREIGN KEY (post_id) REFERENCES blog_posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_category_translations ADD CONSTRAINT FK_video_cat_trans_cat FOREIGN KEY (category_id) REFERENCES video_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_translations ADD CONSTRAINT FK_video_trans_video FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE');

        // Migrate existing BG data
        $this->addSql('INSERT INTO blog_category_translations (category_id, locale, label) SELECT id, \'bg\', label_bg FROM blog_categories');
        $this->addSql('INSERT INTO blog_post_translations (post_id, locale, title, excerpt, content) SELECT id, \'bg\', title_bg, excerpt_bg, content_bg FROM blog_posts');
        $this->addSql('INSERT INTO video_category_translations (category_id, locale, label) SELECT id, \'bg\', label_bg FROM video_categories');
        $this->addSql('INSERT INTO video_translations (video_id, locale, title) SELECT id, \'bg\', title_bg FROM videos');

        // Migrate existing EN data (skip empty titles)
        $this->addSql('INSERT INTO blog_category_translations (category_id, locale, label) SELECT id, \'en\', label_en FROM blog_categories WHERE label_en IS NOT NULL AND label_en != \'\'');
        $this->addSql('INSERT INTO blog_post_translations (post_id, locale, title, excerpt, content) SELECT id, \'en\', title_en, excerpt_en, content_en FROM blog_posts WHERE title_en IS NOT NULL AND title_en != \'\'');
        $this->addSql('INSERT INTO video_category_translations (category_id, locale, label) SELECT id, \'en\', label_en FROM video_categories WHERE label_en IS NOT NULL AND label_en != \'\'');
        $this->addSql('INSERT INTO video_translations (video_id, locale, title) SELECT id, \'en\', title_en FROM videos WHERE title_en IS NOT NULL AND title_en != \'\'');

        // Drop old language columns
        $this->addSql('ALTER TABLE blog_categories DROP COLUMN label_bg, DROP COLUMN label_en');
        $this->addSql('ALTER TABLE blog_posts DROP COLUMN title_bg, DROP COLUMN title_en, DROP COLUMN excerpt_bg, DROP COLUMN excerpt_en, DROP COLUMN content_bg, DROP COLUMN content_en');
        $this->addSql('ALTER TABLE video_categories DROP COLUMN label_bg, DROP COLUMN label_en');
        $this->addSql('ALTER TABLE videos DROP COLUMN title_bg, DROP COLUMN title_en');
    }

    public function down(Schema $schema): void
    {
        // Restore columns (nullable with empty default for safe data restoration)
        $this->addSql('ALTER TABLE blog_categories ADD COLUMN label_bg VARCHAR(255) NOT NULL DEFAULT \'\', ADD COLUMN label_en VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->addSql('ALTER TABLE blog_posts ADD COLUMN title_bg VARCHAR(255) NOT NULL DEFAULT \'\', ADD COLUMN title_en VARCHAR(255) NOT NULL DEFAULT \'\', ADD COLUMN excerpt_bg LONGTEXT DEFAULT NULL, ADD COLUMN excerpt_en LONGTEXT DEFAULT NULL, ADD COLUMN content_bg LONGTEXT DEFAULT NULL, ADD COLUMN content_en LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE video_categories ADD COLUMN label_bg VARCHAR(255) NOT NULL DEFAULT \'\', ADD COLUMN label_en VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->addSql('ALTER TABLE videos ADD COLUMN title_bg VARCHAR(255) NOT NULL DEFAULT \'\', ADD COLUMN title_en VARCHAR(255) NOT NULL DEFAULT \'\'');

        // Restore data from translation tables
        $this->addSql('UPDATE blog_categories bc JOIN blog_category_translations t ON t.category_id = bc.id AND t.locale = \'bg\' SET bc.label_bg = t.label');
        $this->addSql('UPDATE blog_categories bc JOIN blog_category_translations t ON t.category_id = bc.id AND t.locale = \'en\' SET bc.label_en = t.label');
        $this->addSql('UPDATE blog_posts bp JOIN blog_post_translations t ON t.post_id = bp.id AND t.locale = \'bg\' SET bp.title_bg = t.title, bp.excerpt_bg = t.excerpt, bp.content_bg = t.content');
        $this->addSql('UPDATE blog_posts bp JOIN blog_post_translations t ON t.post_id = bp.id AND t.locale = \'en\' SET bp.title_en = t.title, bp.excerpt_en = t.excerpt, bp.content_en = t.content');
        $this->addSql('UPDATE video_categories vc JOIN video_category_translations t ON t.category_id = vc.id AND t.locale = \'bg\' SET vc.label_bg = t.label');
        $this->addSql('UPDATE video_categories vc JOIN video_category_translations t ON t.category_id = vc.id AND t.locale = \'en\' SET vc.label_en = t.label');
        $this->addSql('UPDATE videos v JOIN video_translations t ON t.video_id = v.id AND t.locale = \'bg\' SET v.title_bg = t.title');
        $this->addSql('UPDATE videos v JOIN video_translations t ON t.video_id = v.id AND t.locale = \'en\' SET v.title_en = t.title');

        // Drop translation tables
        $this->addSql('ALTER TABLE blog_category_translations DROP FOREIGN KEY FK_blog_cat_trans_cat');
        $this->addSql('ALTER TABLE blog_post_translations DROP FOREIGN KEY FK_blog_post_trans_post');
        $this->addSql('ALTER TABLE video_category_translations DROP FOREIGN KEY FK_video_cat_trans_cat');
        $this->addSql('ALTER TABLE video_translations DROP FOREIGN KEY FK_video_trans_video');
        $this->addSql('DROP TABLE blog_category_translations');
        $this->addSql('DROP TABLE blog_post_translations');
        $this->addSql('DROP TABLE video_category_translations');
        $this->addSql('DROP TABLE video_translations');
    }
}
