<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118181543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message LONGTEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A1B8AE1EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_request ADD CONSTRAINT FK_A1B8AE1EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_request DROP FOREIGN KEY FK_A1B8AE1EA76ED395');
        $this->addSql('DROP TABLE contact_request');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) NOT NULL');
    }
}
