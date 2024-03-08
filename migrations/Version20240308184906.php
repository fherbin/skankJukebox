<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308184906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE cd_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE track_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE cd (id INT NOT NULL, name VARCHAR(255) NOT NULL, artist VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE track (id INT NOT NULL, cd_id INT NOT NULL, name VARCHAR(255) NOT NULL, length INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D6E3F8A635F486F6 ON track (cd_id)');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A635F486F6 FOREIGN KEY (cd_id) REFERENCES cd (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE cd_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE track_id_seq CASCADE');
        $this->addSql('ALTER TABLE track DROP CONSTRAINT FK_D6E3F8A635F486F6');
        $this->addSql('DROP TABLE cd');
        $this->addSql('DROP TABLE track');
    }
}
