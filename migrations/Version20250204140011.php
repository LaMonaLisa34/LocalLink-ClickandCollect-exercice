<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204140011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE business (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, phone VARCHAR(30) NOT NULL, description VARCHAR(255) NOT NULL, statistic INT NOT NULL, footprint_carbon DOUBLE PRECISION NOT NULL, street_number VARCHAR(10) DEFAULT NULL, street_name VARCHAR(64) NOT NULL, city_name VARCHAR(64) NOT NULL, validated TINYINT(1) NOT NULL, rejection_reason LONGTEXT DEFAULT NULL, lat DOUBLE PRECISION NOT NULL, lon DOUBLE PRECISION NOT NULL, owner_id INT DEFAULT NULL, categories_id INT NOT NULL, INDEX IDX_8D36E387E3C61F9 (owner_id), INDEX IDX_8D36E38A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE business_label (business_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_7046B757A89DB457 (business_id), INDEX IDX_7046B75733B92F39 (label_id), PRIMARY KEY(business_id, label_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE business_photos (id INT AUTO_INCREMENT NOT NULL, photo VARCHAR(255) NOT NULL, business_id INT NOT NULL, INDEX IDX_53D6AF23A89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE business_review (id INT AUTO_INCREMENT NOT NULL, comment VARCHAR(255) DEFAULT NULL, business_score INT NOT NULL, response VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, business_id INT NOT NULL, INDEX IDX_22E3CE3CA76ED395 (user_id), INDEX IDX_22E3CE3CA89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commands (id INT AUTO_INCREMENT NOT NULL, date_command DATE NOT NULL, total_price DOUBLE PRECISION NOT NULL, command_number VARCHAR(32) NOT NULL, status VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, owner_id INT NOT NULL, business_id INT NOT NULL, product_id INT NOT NULL, command_id INT DEFAULT NULL, INDEX IDX_BA388B77E3C61F9 (owner_id), INDEX IDX_BA388B7A89DB457 (business_id), INDEX IDX_BA388B74584665A (product_id), INDEX IDX_BA388B733E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE days (id INT AUTO_INCREMENT NOT NULL, day_name VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, name_event VARCHAR(128) NOT NULL, description_event VARCHAR(255) NOT NULL, begin_date DATE NOT NULL, end_date DATE NOT NULL, business_id INT NOT NULL, INDEX IDX_5387574AA89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE forum_replies (id INT AUTO_INCREMENT NOT NULL, message_reply VARCHAR(255) NOT NULL, date_time_reply DATETIME NOT NULL, topic_id INT NOT NULL, author_forum_reply_id INT NOT NULL, INDEX IDX_51CC2A5B1F55203D (topic_id), INDEX IDX_51CC2A5B737500C2 (author_forum_reply_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE forum_topics (id INT AUTO_INCREMENT NOT NULL, title_topic VARCHAR(64) NOT NULL, message_topic VARCHAR(255) NOT NULL, date_time_topic DATETIME NOT NULL, author_forum_subject_id INT NOT NULL, INDEX IDX_895975E8C231999E (author_forum_subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, photo VARCHAR(255) DEFAULT NULL, product_id INT NOT NULL, INDEX IDX_14B784184584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, opening_hour VARCHAR(32) NOT NULL, closing_hour VARCHAR(32) NOT NULL, days_id INT NOT NULL, INDEX IDX_D499BFF63575FA99 (days_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE planning_business (planning_id INT NOT NULL, business_id INT NOT NULL, INDEX IDX_3FD638953D865311 (planning_id), INDEX IDX_3FD63895A89DB457 (business_id), PRIMARY KEY(planning_id, business_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, description VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, business_id INT DEFAULT NULL, INDEX IDX_D34A04ADA89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_review (id INT AUTO_INCREMENT NOT NULL, score INT NOT NULL, product_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_1B3FC0624584665A (product_id), INDEX IDX_1B3FC062A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE promotions (id INT AUTO_INCREMENT NOT NULL, promotion_name VARCHAR(64) NOT NULL, promotion_description VARCHAR(255) NOT NULL, begin_date DATE NOT NULL, end_date DATE NOT NULL, reduction INT NOT NULL, image_promotion VARCHAR(255) NOT NULL, business_id INT DEFAULT NULL, INDEX IDX_EA1B3034A89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(30) NOT NULL, first_name VARCHAR(32) NOT NULL, last_name VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE business ADD CONSTRAINT FK_8D36E387E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE business ADD CONSTRAINT FK_8D36E38A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE business_label ADD CONSTRAINT FK_7046B757A89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE business_label ADD CONSTRAINT FK_7046B75733B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE business_photos ADD CONSTRAINT FK_53D6AF23A89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE business_review ADD CONSTRAINT FK_22E3CE3CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE business_review ADD CONSTRAINT FK_22E3CE3CA89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B733E1689A FOREIGN KEY (command_id) REFERENCES commands (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AA89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_replies ADD CONSTRAINT FK_51CC2A5B1F55203D FOREIGN KEY (topic_id) REFERENCES forum_topics (id)');
        $this->addSql('ALTER TABLE forum_replies ADD CONSTRAINT FK_51CC2A5B737500C2 FOREIGN KEY (author_forum_reply_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE forum_topics ADD CONSTRAINT FK_895975E8C231999E FOREIGN KEY (author_forum_subject_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784184584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF63575FA99 FOREIGN KEY (days_id) REFERENCES days (id)');
        $this->addSql('ALTER TABLE planning_business ADD CONSTRAINT FK_3FD638953D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning_business ADD CONSTRAINT FK_3FD63895A89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_review ADD CONSTRAINT FK_1B3FC0624584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_review ADD CONSTRAINT FK_1B3FC062A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promotions ADD CONSTRAINT FK_EA1B3034A89DB457 FOREIGN KEY (business_id) REFERENCES business (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE business DROP FOREIGN KEY FK_8D36E387E3C61F9');
        $this->addSql('ALTER TABLE business DROP FOREIGN KEY FK_8D36E38A21214B7');
        $this->addSql('ALTER TABLE business_label DROP FOREIGN KEY FK_7046B757A89DB457');
        $this->addSql('ALTER TABLE business_label DROP FOREIGN KEY FK_7046B75733B92F39');
        $this->addSql('ALTER TABLE business_photos DROP FOREIGN KEY FK_53D6AF23A89DB457');
        $this->addSql('ALTER TABLE business_review DROP FOREIGN KEY FK_22E3CE3CA76ED395');
        $this->addSql('ALTER TABLE business_review DROP FOREIGN KEY FK_22E3CE3CA89DB457');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B77E3C61F9');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A89DB457');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B733E1689A');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AA89DB457');
        $this->addSql('ALTER TABLE forum_replies DROP FOREIGN KEY FK_51CC2A5B1F55203D');
        $this->addSql('ALTER TABLE forum_replies DROP FOREIGN KEY FK_51CC2A5B737500C2');
        $this->addSql('ALTER TABLE forum_topics DROP FOREIGN KEY FK_895975E8C231999E');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784184584665A');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF63575FA99');
        $this->addSql('ALTER TABLE planning_business DROP FOREIGN KEY FK_3FD638953D865311');
        $this->addSql('ALTER TABLE planning_business DROP FOREIGN KEY FK_3FD63895A89DB457');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA89DB457');
        $this->addSql('ALTER TABLE product_review DROP FOREIGN KEY FK_1B3FC0624584665A');
        $this->addSql('ALTER TABLE product_review DROP FOREIGN KEY FK_1B3FC062A76ED395');
        $this->addSql('ALTER TABLE promotions DROP FOREIGN KEY FK_EA1B3034A89DB457');
        $this->addSql('DROP TABLE business');
        $this->addSql('DROP TABLE business_label');
        $this->addSql('DROP TABLE business_photos');
        $this->addSql('DROP TABLE business_review');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE commands');
        $this->addSql('DROP TABLE days');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE forum_replies');
        $this->addSql('DROP TABLE forum_topics');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE planning_business');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_review');
        $this->addSql('DROP TABLE promotions');
        $this->addSql('DROP TABLE user');
    }
}
