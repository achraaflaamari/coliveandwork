<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026122637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coliving_space ADD coliving_city_id INT NOT NULL');
        $this->addSql('ALTER TABLE coliving_space ADD CONSTRAINT FK_9C2ADBAD48CE3250 FOREIGN KEY (coliving_city_id) REFERENCES coliving_city (id)');
        $this->addSql('CREATE INDEX IDX_9C2ADBAD48CE3250 ON coliving_space (coliving_city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coliving_space DROP FOREIGN KEY FK_9C2ADBAD48CE3250');
        $this->addSql('DROP INDEX IDX_9C2ADBAD48CE3250 ON coliving_space');
        $this->addSql('ALTER TABLE coliving_space DROP coliving_city_id');
    }
}
