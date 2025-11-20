<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118190440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample ADD contact_request_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C385C7E132 FOREIGN KEY (contact_request_id) REFERENCES contact_request (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F10B76C385C7E132 ON sample (contact_request_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C385C7E132');
        $this->addSql('DROP INDEX UNIQ_F10B76C385C7E132 ON sample');
        $this->addSql('ALTER TABLE sample DROP contact_request_id');
    }
}
