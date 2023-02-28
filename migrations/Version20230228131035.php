<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228131035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance DROP INDEX UNIQ_ACF41FFE4584665A, ADD INDEX IDX_ACF41FFE4584665A (product_id)');
        $this->addSql('ALTER TABLE statement DROP INDEX UNIQ_C0DB51764584665A, ADD INDEX IDX_C0DB51764584665A (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance DROP INDEX IDX_ACF41FFE4584665A, ADD UNIQUE INDEX UNIQ_ACF41FFE4584665A (product_id)');
        $this->addSql('ALTER TABLE statement DROP INDEX IDX_C0DB51764584665A, ADD UNIQUE INDEX UNIQ_C0DB51764584665A (product_id)');
    }
}
