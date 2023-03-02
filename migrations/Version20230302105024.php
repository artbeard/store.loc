<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302105024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE balance (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, amount INT NOT NULL, cost DOUBLE PRECISION NOT NULL, balance_at DATE NOT NULL, INDEX IDX_ACF41FFE4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preorder (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, document_prop VARCHAR(50) NOT NULL, amount INT NOT NULL, ordered_at DATE NOT NULL, price DOUBLE PRECISION DEFAULT NULL, sent_at DATE DEFAULT NULL, INDEX IDX_D9B775974584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statement (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, document_prop VARCHAR(50) NOT NULL, post_type VARCHAR(10) NOT NULL, posted_at DATE NOT NULL, amount INT NOT NULL, cost DOUBLE PRECISION NOT NULL, INDEX IDX_C0DB51764584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE balance ADD CONSTRAINT FK_ACF41FFE4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE preorder ADD CONSTRAINT FK_D9B775974584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE statement ADD CONSTRAINT FK_C0DB51764584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance DROP FOREIGN KEY FK_ACF41FFE4584665A');
        $this->addSql('ALTER TABLE preorder DROP FOREIGN KEY FK_D9B775974584665A');
        $this->addSql('ALTER TABLE statement DROP FOREIGN KEY FK_C0DB51764584665A');
        $this->addSql('DROP TABLE balance');
        $this->addSql('DROP TABLE preorder');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE statement');
    }
}
