<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260108014621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, total DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, shipping_address VARCHAR(255) NOT NULL, payment_method VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, unit_price DOUBLE PRECISION NOT NULL, order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_52EA1F098D9F6D38 (order_id), INDEX IDX_52EA1F094584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, stock INT NOT NULL, image VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, seller_id INT NOT NULL, category_id INT DEFAULT NULL, INDEX IDX_D34A04AD8DE820D9 (seller_id), INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE seller_profile (id INT AUTO_INCREMENT NOT NULL, shop_name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_5A59131EA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, profile_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD8DE820D9 FOREIGN KEY (seller_id) REFERENCES seller_profile (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE seller_profile ADD CONSTRAINT FK_5A59131EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F094584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD8DE820D9');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE seller_profile DROP FOREIGN KEY FK_5A59131EA76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE seller_profile');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
