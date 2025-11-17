<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117175311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307fcd53edb6');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307ff624b39d');
        $this->addSql('ALTER TABLE verification_user DROP CONSTRAINT fk_12a7254e69f4b775');
        $this->addSql('ALTER TABLE verification_user DROP CONSTRAINT fk_12a7254ea76ed395');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c8495519eb6921');
        $this->addSql('ALTER TABLE coliving_space DROP CONSTRAINT fk_9c2adbad7e3c61f9');
        $this->addSql('ALTER TABLE verification_space DROP CONSTRAINT fk_b789606aa76ed395');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, address_id INT DEFAULT NULL, photo_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, gender BOOLEAN DEFAULT NULL, birth_date DATE DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, is_email_verified BOOLEAN DEFAULT false NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE INDEX IDX_1483A5E9F5B7AF75 ON users (address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E97E9E4C8C ON users (photo_id)');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E97E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d6497e9e4c8c');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649f5b7af75');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('ALTER TABLE coliving_space DROP CONSTRAINT FK_9C2ADBAD7E3C61F9');
        $this->addSql('ALTER TABLE coliving_space ADD CONSTRAINT FK_9C2ADBAD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C8495519EB6921');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verification_space DROP CONSTRAINT FK_B789606AA76ED395');
        $this->addSql('ALTER TABLE verification_space ADD CONSTRAINT FK_B789606AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verification_user DROP CONSTRAINT FK_12A7254EA76ED395');
        $this->addSql('DROP INDEX idx_12a7254e69f4b775');
        $this->addSql('ALTER TABLE verification_user RENAME COLUMN verified_by_id TO owner_id');
        $this->addSql('ALTER TABLE verification_user ADD CONSTRAINT FK_12A7254E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verification_user ADD CONSTRAINT FK_12A7254EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_12A7254E7E3C61F9 ON verification_user (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE coliving_space DROP CONSTRAINT FK_9C2ADBAD7E3C61F9');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C8495519EB6921');
        $this->addSql('ALTER TABLE verification_space DROP CONSTRAINT FK_B789606AA76ED395');
        $this->addSql('ALTER TABLE verification_user DROP CONSTRAINT FK_12A7254E7E3C61F9');
        $this->addSql('ALTER TABLE verification_user DROP CONSTRAINT FK_12A7254EA76ED395');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, address_id INT DEFAULT NULL, photo_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, gender BOOLEAN DEFAULT NULL, birth_date DATE DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, is_email_verified BOOLEAN DEFAULT false NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8d93d649f5b7af75 ON "user" (address_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d6497e9e4c8c ON "user" (photo_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d6497e9e4c8c FOREIGN KEY (photo_id) REFERENCES photo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649f5b7af75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9F5B7AF75');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E97E9E4C8C');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307ff624b39d');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307fcd53edb6');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307ff624b39d FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307fcd53edb6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verification_user DROP CONSTRAINT fk_12a7254ea76ed395');
        $this->addSql('DROP INDEX IDX_12A7254E7E3C61F9');
        $this->addSql('ALTER TABLE verification_user RENAME COLUMN owner_id TO verified_by_id');
        $this->addSql('ALTER TABLE verification_user ADD CONSTRAINT fk_12a7254e69f4b775 FOREIGN KEY (verified_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verification_user ADD CONSTRAINT fk_12a7254ea76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_12a7254e69f4b775 ON verification_user (verified_by_id)');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c8495519eb6921');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c8495519eb6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE coliving_space DROP CONSTRAINT fk_9c2adbad7e3c61f9');
        $this->addSql('ALTER TABLE coliving_space ADD CONSTRAINT fk_9c2adbad7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verification_space DROP CONSTRAINT fk_b789606aa76ed395');
        $this->addSql('ALTER TABLE verification_space ADD CONSTRAINT fk_b789606aa76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
