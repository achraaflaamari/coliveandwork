<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024071656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coliving_space_amenity (amenity_id INT NOT NULL, coliving_space_id INT NOT NULL, INDEX IDX_F565D1BD9F9F1305 (amenity_id), INDEX IDX_F565D1BDE96DE9D (coliving_space_id), PRIMARY KEY(amenity_id, coliving_space_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coliving_space_amenity ADD CONSTRAINT FK_F565D1BD9F9F1305 FOREIGN KEY (amenity_id) REFERENCES amenity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coliving_space_amenity ADD CONSTRAINT FK_F565D1BDE96DE9D FOREIGN KEY (coliving_space_id) REFERENCES coliving_space (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coliving_space_amenity DROP FOREIGN KEY FK_F565D1BD9F9F1305');
        $this->addSql('ALTER TABLE coliving_space_amenity DROP FOREIGN KEY FK_F565D1BDE96DE9D');
        $this->addSql('DROP TABLE coliving_space_amenity');
    }
}
