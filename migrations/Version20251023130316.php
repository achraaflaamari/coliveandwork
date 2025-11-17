<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251023130316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, coliving_city_id INT NOT NULL, type_address VARCHAR(50) DEFAULT NULL, street_number VARCHAR(10) DEFAULT NULL, street_name VARCHAR(100) NOT NULL, postal_code VARCHAR(20) NOT NULL, other_city_name VARCHAR(100) DEFAULT NULL, region_name VARCHAR(100) DEFAULT NULL, country_name VARCHAR(100) DEFAULT NULL, longitude NUMERIC(9, 6) DEFAULT NULL, latitude NUMERIC(9, 6) DEFAULT NULL, INDEX IDX_D4E6F8148CE3250 (coliving_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amenity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, amenity_type VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, icon_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amenity_private_space (amenity_id INT NOT NULL, private_space_id INT NOT NULL, INDEX IDX_9DB850719F9F1305 (amenity_id), INDEX IDX_9DB8507184C18CED (private_space_id), PRIMARY KEY(amenity_id, private_space_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coliving_city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coliving_space (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, owner_id INT NOT NULL, title_space VARCHAR(100) NOT NULL, description_space LONGTEXT NOT NULL, housing_type VARCHAR(50) NOT NULL, room_count INT NOT NULL, total_area_m2 NUMERIC(6, 2) NOT NULL, capacity_max INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_active TINYINT(1) NOT NULL, INDEX IDX_9C2ADBADF5B7AF75 (address_id), INDEX IDX_9C2ADBAD7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coliving_space_amenity (coliving_space_id INT NOT NULL, amenity_id INT NOT NULL, INDEX IDX_F565D1BDE96DE9D (coliving_space_id), INDEX IDX_F565D1BD9F9F1305 (amenity_id), PRIMARY KEY(coliving_space_id, amenity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, receiver_id INT NOT NULL, sender_id INT NOT NULL, content LONGTEXT NOT NULL, send_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', seen_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6BD307FCD53EDB6 (receiver_id), INDEX IDX_B6BD307FF624B39D (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, coliving_space_id INT NOT NULL, private_space_id INT DEFAULT NULL, photo_url VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_main TINYINT(1) NOT NULL, uploaded_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_14B78418E96DE9D (coliving_space_id), INDEX IDX_14B7841884C18CED (private_space_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE private_space (id INT AUTO_INCREMENT NOT NULL, coliving_space_id INT NOT NULL, title_private_space VARCHAR(50) NOT NULL, description_private_space LONGTEXT NOT NULL, capacity INT NOT NULL, area_m2 NUMERIC(6, 2) NOT NULL, price_per_month NUMERIC(7, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_active TINYINT(1) NOT NULL, INDEX IDX_53B9DC5E96DE9D (coliving_space_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, private_space_id INT NOT NULL, client_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, is_for_two TINYINT(1) NOT NULL, lodging_tax NUMERIC(6, 2) NOT NULL, total_price NUMERIC(6, 2) NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_42C8495584C18CED (private_space_id), INDEX IDX_42C8495519EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, rating NUMERIC(3, 2) NOT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_794381C6B83297E7 (reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verification_space (id INT AUTO_INCREMENT NOT NULL, coliving_space_id INT NOT NULL, private_space_id INT DEFAULT NULL, user_id INT NOT NULL, verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(50) NOT NULL, notes LONGTEXT DEFAULT NULL, capacity_max VARCHAR(255) NOT NULL, INDEX IDX_B789606AE96DE9D (coliving_space_id), INDEX IDX_B789606A84C18CED (private_space_id), INDEX IDX_B789606AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verification_user (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, user_id INT NOT NULL, document_type VARCHAR(50) NOT NULL, document_url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(50) NOT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_12A7254E7E3C61F9 (owner_id), INDEX IDX_12A7254EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8148CE3250 FOREIGN KEY (coliving_city_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE amenity_private_space ADD CONSTRAINT FK_9DB850719F9F1305 FOREIGN KEY (amenity_id) REFERENCES amenity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE amenity_private_space ADD CONSTRAINT FK_9DB8507184C18CED FOREIGN KEY (private_space_id) REFERENCES private_space (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coliving_space ADD CONSTRAINT FK_9C2ADBADF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE coliving_space ADD CONSTRAINT FK_9C2ADBAD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE coliving_space_amenity ADD CONSTRAINT FK_F565D1BDE96DE9D FOREIGN KEY (coliving_space_id) REFERENCES coliving_space (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coliving_space_amenity ADD CONSTRAINT FK_F565D1BD9F9F1305 FOREIGN KEY (amenity_id) REFERENCES amenity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418E96DE9D FOREIGN KEY (coliving_space_id) REFERENCES coliving_space (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841884C18CED FOREIGN KEY (private_space_id) REFERENCES private_space (id)');
        $this->addSql('ALTER TABLE private_space ADD CONSTRAINT FK_53B9DC5E96DE9D FOREIGN KEY (coliving_space_id) REFERENCES coliving_space (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495584C18CED FOREIGN KEY (private_space_id) REFERENCES private_space (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE verification_space ADD CONSTRAINT FK_B789606AE96DE9D FOREIGN KEY (coliving_space_id) REFERENCES coliving_space (id)');
        $this->addSql('ALTER TABLE verification_space ADD CONSTRAINT FK_B789606A84C18CED FOREIGN KEY (private_space_id) REFERENCES private_space (id)');
        $this->addSql('ALTER TABLE verification_space ADD CONSTRAINT FK_B789606AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE verification_user ADD CONSTRAINT FK_12A7254E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE verification_user ADD CONSTRAINT FK_12A7254EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD address_id INT DEFAULT NULL, ADD photo_id INT DEFAULT NULL, CHANGE email_verified is_email_verified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F5B7AF75 ON user (address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497E9E4C8C ON user (photo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F5B7AF75');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497E9E4C8C');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F8148CE3250');
        $this->addSql('ALTER TABLE amenity_private_space DROP FOREIGN KEY FK_9DB850719F9F1305');
        $this->addSql('ALTER TABLE amenity_private_space DROP FOREIGN KEY FK_9DB8507184C18CED');
        $this->addSql('ALTER TABLE coliving_space DROP FOREIGN KEY FK_9C2ADBADF5B7AF75');
        $this->addSql('ALTER TABLE coliving_space DROP FOREIGN KEY FK_9C2ADBAD7E3C61F9');
        $this->addSql('ALTER TABLE coliving_space_amenity DROP FOREIGN KEY FK_F565D1BDE96DE9D');
        $this->addSql('ALTER TABLE coliving_space_amenity DROP FOREIGN KEY FK_F565D1BD9F9F1305');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418E96DE9D');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841884C18CED');
        $this->addSql('ALTER TABLE private_space DROP FOREIGN KEY FK_53B9DC5E96DE9D');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495584C18CED');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495519EB6921');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6B83297E7');
        $this->addSql('ALTER TABLE verification_space DROP FOREIGN KEY FK_B789606AE96DE9D');
        $this->addSql('ALTER TABLE verification_space DROP FOREIGN KEY FK_B789606A84C18CED');
        $this->addSql('ALTER TABLE verification_space DROP FOREIGN KEY FK_B789606AA76ED395');
        $this->addSql('ALTER TABLE verification_user DROP FOREIGN KEY FK_12A7254E7E3C61F9');
        $this->addSql('ALTER TABLE verification_user DROP FOREIGN KEY FK_12A7254EA76ED395');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE amenity');
        $this->addSql('DROP TABLE amenity_private_space');
        $this->addSql('DROP TABLE coliving_city');
        $this->addSql('DROP TABLE coliving_space');
        $this->addSql('DROP TABLE coliving_space_amenity');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE private_space');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE verification_space');
        $this->addSql('DROP TABLE verification_user');
        $this->addSql('DROP INDEX IDX_8D93D649F5B7AF75 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D6497E9E4C8C ON user');
        $this->addSql('ALTER TABLE user DROP address_id, DROP photo_id, CHANGE is_email_verified email_verified TINYINT(1) NOT NULL');
    }
}
