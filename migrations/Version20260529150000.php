<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260529150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add answered_by_specialist_id FK to specialist_questions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE specialist_questions ADD answered_by_specialist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE specialist_questions ADD CONSTRAINT FK_SQ_ANSWERED_BY FOREIGN KEY (answered_by_specialist_id) REFERENCES specialists (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_SQ_ANSWERED_BY ON specialist_questions (answered_by_specialist_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE specialist_questions DROP FOREIGN KEY FK_SQ_ANSWERED_BY');
        $this->addSql('DROP INDEX IDX_SQ_ANSWERED_BY ON specialist_questions');
        $this->addSql('ALTER TABLE specialist_questions DROP COLUMN answered_by_specialist_id');
    }
}
