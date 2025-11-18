<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118084054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hamster ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE hamster ADD CONSTRAINT FK_BCCF2F37E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BCCF2F37E3C61F9 ON hamster (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hamster DROP FOREIGN KEY FK_BCCF2F37E3C61F9');
        $this->addSql('DROP INDEX IDX_BCCF2F37E3C61F9 ON hamster');
        $this->addSql('ALTER TABLE hamster DROP owner_id');
    }
}
