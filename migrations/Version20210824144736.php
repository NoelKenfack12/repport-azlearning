<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210824144736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE continent (id INT AUTO_INCREMENT NOT NULL, src VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, siteweb VARCHAR(255) DEFAULT NULL, nom VARCHAR(100) NOT NULL, citoyen VARCHAR(100) DEFAULT NULL, citoyenne VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_6CC70C7C8535EB27 (siteweb), UNIQUE INDEX UNIQ_6CC70C7C6C6E55B5 (nom), UNIQUE INDEX UNIQ_6CC70C7C8E7EF6AC (citoyen), UNIQUE INDEX UNIQ_6CC70C7CD113EBB3 (citoyenne), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drapeau (id INT AUTO_INCREMENT NOT NULL, src VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pays (id INT AUTO_INCREMENT NOT NULL, drapeau_id INT DEFAULT NULL, continent_id INT NOT NULL, nom VARCHAR(255) NOT NULL, siteweb VARCHAR(255) DEFAULT NULL, citoyen VARCHAR(255) DEFAULT NULL, citoyenne VARCHAR(255) DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, domaine VARCHAR(255) DEFAULT NULL, src VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_349F3CAE6C6E55B5 (nom), UNIQUE INDEX UNIQ_349F3CAE8535EB27 (siteweb), UNIQUE INDEX UNIQ_349F3CAE77153098 (code), UNIQUE INDEX UNIQ_349F3CAE78AF0ACC (domaine), UNIQUE INDEX UNIQ_349F3CAE2C70DBB9 (drapeau_id), INDEX IDX_349F3CAE921F4C77 (continent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pays ADD CONSTRAINT FK_349F3CAE2C70DBB9 FOREIGN KEY (drapeau_id) REFERENCES drapeau (id)');
        $this->addSql('ALTER TABLE pays ADD CONSTRAINT FK_349F3CAE921F4C77 FOREIGN KEY (continent_id) REFERENCES continent (id)');
        $this->addSql('ALTER TABLE user ADD country_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES pays (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F92F3E70 ON user (country_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pays DROP FOREIGN KEY FK_349F3CAE921F4C77');
        $this->addSql('ALTER TABLE pays DROP FOREIGN KEY FK_349F3CAE2C70DBB9');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('DROP TABLE continent');
        $this->addSql('DROP TABLE drapeau');
        $this->addSql('DROP TABLE pays');
        $this->addSql('DROP INDEX IDX_8D93D649F92F3E70 ON user');
        $this->addSql('ALTER TABLE user DROP country_id');
    }
}
