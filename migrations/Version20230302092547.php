<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302092547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance CHANGE cost cost DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE preorder CHANGE price price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE statement CHANGE cost cost DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance CHANGE cost cost INT NOT NULL');
        $this->addSql('ALTER TABLE preorder CHANGE price price INT DEFAULT NULL');
        $this->addSql('ALTER TABLE statement CHANGE cost cost INT NOT NULL');
    }
}
