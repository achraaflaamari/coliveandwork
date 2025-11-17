<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117161537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE verification_user DROP CONSTRAINT fk_12a7254e69f4b775');
        $this->addSql('DROP INDEX idx_12a7254e69f4b775');
        $this->addSql('ALTER TABLE verification_user RENAME COLUMN verified_by_id TO owner_id');
        $this->addSql('ALTER TABLE verification_user ADD CONSTRAINT FK_12A7254E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_12A7254E7E3C61F9 ON verification_user (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE verification_user DROP CONSTRAINT FK_12A7254E7E3C61F9');
        $this->addSql('DROP INDEX IDX_12A7254E7E3C61F9');
        $this->addSql('ALTER TABLE verification_user RENAME COLUMN owner_id TO verified_by_id');
        $this->addSql('ALTER TABLE verification_user ADD CONSTRAINT fk_12a7254e69f4b775 FOREIGN KEY (verified_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_12a7254e69f4b775 ON verification_user (verified_by_id)');
    }
}
