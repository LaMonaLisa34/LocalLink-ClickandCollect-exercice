<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205102936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A89DB457');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B77E3C61F9');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B733E1689A');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE commands');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, owner_id INT NOT NULL, business_id INT NOT NULL, product_id INT NOT NULL, command_id INT DEFAULT NULL, INDEX IDX_BA388B733E1689A (command_id), INDEX IDX_BA388B74584665A (product_id), INDEX IDX_BA388B77E3C61F9 (owner_id), INDEX IDX_BA388B7A89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commands (id INT AUTO_INCREMENT NOT NULL, date_command DATE NOT NULL, total_price DOUBLE PRECISION NOT NULL, command_number VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, status VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B733E1689A FOREIGN KEY (command_id) REFERENCES commands (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
