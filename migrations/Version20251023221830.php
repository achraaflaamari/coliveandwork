<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251023221830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE private_space_amenity (private_space_id INT NOT NULL, amenity_id INT NOT NULL, INDEX IDX_C52CBEAF84C18CED (private_space_id), INDEX IDX_C52CBEAF9F9F1305 (amenity_id), PRIMARY KEY(private_space_id, amenity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE private_space_amenity ADD CONSTRAINT FK_C52CBEAF84C18CED FOREIGN KEY (private_space_id) REFERENCES private_space (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE private_space_amenity ADD CONSTRAINT FK_C52CBEAF9F9F1305 FOREIGN KEY (amenity_id) REFERENCES amenity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE amenity_private_space DROP FOREIGN KEY FK_9DB8507184C18CED');
        $this->addSql('ALTER TABLE amenity_private_space DROP FOREIGN KEY FK_9DB850719F9F1305');
        $this->addSql('ALTER TABLE coliving_space_amenity DROP FOREIGN KEY FK_F565D1BD9F9F1305');
        $this->addSql('ALTER TABLE coliving_space_amenity DROP FOREIGN KEY FK_F565D1BDE96DE9D');
        $this->addSql('DROP TABLE amenity_private_space');
        $this->addSql('DROP TABLE coliving_space_amenity');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F8148CE3250');
        $this->addSql('ALTER TABLE address DROP type_address, CHANGE coliving_city_id coliving_city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8148CE3250 FOREIGN KEY (coliving_city_id) REFERENCES address (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE coliving_space CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL, CHANGE title_space title_coliving_space VARCHAR(100) NOT NULL, CHANGE description_space description_coliving_space LONGTEXT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user CHANGE is_email_verified is_email_verified TINYINT(1) DEFAULT 0 NOT NULL, CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE verification_space DROP capacity_max');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE amenity_private_space (amenity_id INT NOT NULL, private_space_id INT NOT NULL, INDEX IDX_9DB850719F9F1305 (amenity_id), INDEX IDX_9DB8507184C18CED (private_space_id), PRIMARY KEY(amenity_id, private_space_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE coliving_space_amenity (coliving_space_id INT NOT NULL, amenity_id INT NOT NULL, INDEX IDX_F565D1BDE96DE9D (coliving_space_id), INDEX IDX_F565D1BD9F9F1305 (amenity_id), PRIMARY KEY(coliving_space_id, amenity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE amenity_private_space ADD CONSTRAINT FK_9DB8507184C18CED FOREIGN KEY (private_space_id) REFERENCES private_space (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE amenity_private_space ADD CONSTRAINT FK_9DB850719F9F1305 FOREIGN KEY (amenity_id) REFERENCES amenity (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coliving_space_amenity ADD CONSTRAINT FK_F565D1BD9F9F1305 FOREIGN KEY (amenity_id) REFERENCES amenity (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coliving_space_amenity ADD CONSTRAINT FK_F565D1BDE96DE9D FOREIGN KEY (coliving_space_id) REFERENCES coliving_space (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE private_space_amenity DROP FOREIGN KEY FK_C52CBEAF84C18CED');
        $this->addSql('ALTER TABLE private_space_amenity DROP FOREIGN KEY FK_C52CBEAF9F9F1305');
        $this->addSql('DROP TABLE private_space_amenity');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F8148CE3250');
        $this->addSql('ALTER TABLE address ADD type_address VARCHAR(50) DEFAULT NULL, CHANGE coliving_city_id coliving_city_id INT NOT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8148CE3250 FOREIGN KEY (coliving_city_id) REFERENCES address (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE coliving_space CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE title_coliving_space title_space VARCHAR(100) NOT NULL, CHANGE description_coliving_space description_space LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE is_email_verified is_email_verified TINYINT(1) NOT NULL, CHANGE is_active is_active TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('ALTER TABLE verification_space ADD capacity_max VARCHAR(255) NOT NULL');
    }
}
