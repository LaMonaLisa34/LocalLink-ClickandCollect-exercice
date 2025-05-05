<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203090200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, owner_id INT NOT NULL, business_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_BA388B77E3C61F9 (owner_id), INDEX IDX_BA388B7A89DB457 (business_id), INDEX IDX_BA388B74584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        // $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        // $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
        // $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        // $this->addSql('ALTER TABLE commands ADD cart_id INT NOT NULL');
        // $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132C1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        // $this->addSql('CREATE INDEX IDX_9A3E132C1AD5CDBF ON commands (cart_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B77E3C61F9');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A89DB457');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('DROP TABLE cart');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132C1AD5CDBF');
        $this->addSql('DROP INDEX IDX_9A3E132C1AD5CDBF ON commands');
        $this->addSql('ALTER TABLE commands DROP cart_id');
    }
}
