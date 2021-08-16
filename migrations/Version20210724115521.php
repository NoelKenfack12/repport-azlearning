<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210724115521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_commentaireblog');
        $this->addSql('ALTER TABLE produitformation RENAME INDEX idx_50a02db1ed5ca9e6 TO IDX_479F56CCED5CA9E6');
        $this->addSql('ALTER TABLE produitformation RENAME INDEX idx_50a02db1f347efb TO IDX_479F56CCF347EFB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_commentaireblog (user_id INT NOT NULL, commentaireblog_id INT NOT NULL, INDEX IDX_4C34330A76ED395 (user_id), INDEX IDX_4C3433037574993 (commentaireblog_id), PRIMARY KEY(user_id, commentaireblog_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_commentaireblog ADD CONSTRAINT FK_4C3433037574993 FOREIGN KEY (commentaireblog_id) REFERENCES commentaireblog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_commentaireblog ADD CONSTRAINT FK_4C34330A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produitformation RENAME INDEX idx_479f56ccf347efb TO IDX_50A02DB1F347EFB');
        $this->addSql('ALTER TABLE produitformation RENAME INDEX idx_479f56cced5ca9e6 TO IDX_50A02DB1ED5CA9E6');
    }
}
