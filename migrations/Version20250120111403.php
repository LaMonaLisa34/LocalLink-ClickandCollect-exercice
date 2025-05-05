<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120111403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE business ADD status VARCHAR(50) DEFAULT \'en attente\' NOT NULL, ADD rejection_reason LONGTEXT DEFAULT NULL');
        // $this->addSql('ALTER TABLE commands CHANGE date_command date_command DATETIME NOT NULL');
        // $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA89DB457');
        // $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
        // $this->addSql('ALTER TABLE product_review DROP FOREIGN KEY FK_1B3FC0624584665A');
        // $this->addSql('ALTER TABLE product_review ADD CONSTRAINT FK_1B3FC0624584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        // $this->addSql('ALTER TABLE promotions DROP FOREIGN KEY FK_EA1B3034A89DB457');
        // $this->addSql('ALTER TABLE promotions ADD CONSTRAINT FK_EA1B3034A89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commands CHANGE date_command date_command DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA89DB457');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promotions DROP FOREIGN KEY FK_EA1B3034A89DB457');
        $this->addSql('ALTER TABLE promotions ADD CONSTRAINT FK_EA1B3034A89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_review DROP FOREIGN KEY FK_1B3FC0624584665A');
        $this->addSql('ALTER TABLE product_review ADD CONSTRAINT FK_1B3FC0624584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE business DROP status, DROP rejection_reason');
    }
}
