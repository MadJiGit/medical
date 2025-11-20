<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118151803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sample (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, sample_type VARCHAR(255) DEFAULT NULL, sent_sample TINYINT(1) NOT NULL, sample_manufacture VARCHAR(255) DEFAULT NULL, sent_date DATE DEFAULT NULL, confirm_received_sample TINYINT(1) NOT NULL, confirm_received_date DATE DEFAULT NULL, user_feedback LONGTEXT DEFAULT NULL, returned_sample TINYINT(1) NOT NULL, returned_date DATE DEFAULT NULL, returned_reason LONGTEXT DEFAULT NULL, INDEX IDX_F10B76C3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, name VARCHAR(255) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, text LONGTEXT DEFAULT NULL, is_active TINYINT(1) NOT NULL, is_banned TINYINT(1) NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, token_expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C3A76ED395');
        $this->addSql('DROP TABLE sample');
        $this->addSql('DROP TABLE user');
    }
}
