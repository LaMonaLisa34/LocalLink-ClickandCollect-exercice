<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120154039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE business DROP FOREIGN KEY FK_8D36E387E3C61F9');
        // $this->addSql('ALTER TABLE business ADD lat DOUBLE PRECISION NOT NULL, ADD lon DOUBLE PRECISION NOT NULL');
        // $this->addSql('ALTER TABLE business ADD CONSTRAINT FK_8D36E387E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        // $this->addSql('ALTER TABLE commands CHANGE date_command date_command DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commands CHANGE date_command date_command DATETIME NOT NULL');
        $this->addSql('ALTER TABLE business DROP FOREIGN KEY FK_8D36E387E3C61F9');
        $this->addSql('ALTER TABLE business DROP lat, DROP lon');
        $this->addSql('ALTER TABLE business ADD CONSTRAINT FK_8D36E387E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
