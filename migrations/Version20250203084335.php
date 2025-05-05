<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203084335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE carts_product DROP FOREIGN KEY FK_5D58DB0CBCB5C6F5');
        // $this->addSql('ALTER TABLE carts_product DROP FOREIGN KEY FK_5D58DB0C4584665A');
        // $this->addSql('ALTER TABLE carts DROP FOREIGN KEY FK_4E004AAC96A34B60');
        // $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132C1AD5CDBF');
        // $this->addSql('DROP TABLE carts_product');
        // $this->addSql('DROP TABLE carts');
        // $this->addSql('DROP INDEX UNIQ_9A3E132C1AD5CDBF ON commands');
        // $this->addSql('ALTER TABLE commands DROP status_cart, DROP cart_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carts_product (carts_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_5D58DB0CBCB5C6F5 (carts_id), INDEX IDX_5D58DB0C4584665A (product_id), PRIMARY KEY(carts_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE carts (id INT AUTO_INCREMENT NOT NULL, quantity_cart INT NOT NULL, owner_cart_id INT DEFAULT NULL, INDEX IDX_4E004AAC96A34B60 (owner_cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE carts_product ADD CONSTRAINT FK_5D58DB0CBCB5C6F5 FOREIGN KEY (carts_id) REFERENCES carts (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carts_product ADD CONSTRAINT FK_5D58DB0C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004AAC96A34B60 FOREIGN KEY (owner_cart_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commands ADD status_cart VARCHAR(32) NOT NULL, ADD cart_id INT NOT NULL');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132C1AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9A3E132C1AD5CDBF ON commands (cart_id)');
    }
}
