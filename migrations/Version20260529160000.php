<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260529160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add institution/address/phone to specialists; add display toggles to specialist_questions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE specialists ADD institution VARCHAR(255) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(50) DEFAULT NULL');

        $this->addSql('ALTER TABLE specialist_questions
            ADD show_specialist_name TINYINT(1) NOT NULL DEFAULT 1,
            ADD show_specialist_title TINYINT(1) NOT NULL DEFAULT 1,
            ADD show_specialist_institution TINYINT(1) NOT NULL DEFAULT 0,
            ADD show_specialist_address TINYINT(1) NOT NULL DEFAULT 0,
            ADD show_specialist_phone TINYINT(1) NOT NULL DEFAULT 0,
            ADD show_specialist_email TINYINT(1) NOT NULL DEFAULT 0
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE specialists DROP COLUMN institution, DROP COLUMN address, DROP COLUMN phone');

        $this->addSql('ALTER TABLE specialist_questions
            DROP COLUMN show_specialist_name,
            DROP COLUMN show_specialist_title,
            DROP COLUMN show_specialist_institution,
            DROP COLUMN show_specialist_address,
            DROP COLUMN show_specialist_phone,
            DROP COLUMN show_specialist_email
        ');
    }
}
