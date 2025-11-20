<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118191324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample ADD recipient_name VARCHAR(255) DEFAULT NULL, ADD recipient_phone VARCHAR(50) DEFAULT NULL, ADD shipping_address VARCHAR(255) DEFAULT NULL, ADD shipping_city VARCHAR(100) DEFAULT NULL, ADD shipping_postal_code VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample DROP recipient_name, DROP recipient_phone, DROP shipping_address, DROP shipping_city, DROP shipping_postal_code');
    }
}
