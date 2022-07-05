<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210710112838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clients (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, societe VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) DEFAULT NULL, rgpd TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C82E74E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE plage_horaire (id INT AUTO_INCREMENT NOT NULL, horaire DATETIME NOT NULL, horaire_prise TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE prestations (id INT AUTO_INCREMENT NOT NULL, type_prestations VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE rdv (id INT AUTO_INCREMENT NOT NULL, prestations_id INT NOT NULL, type_rdv_id INT NOT NULL, horaire_id INT NOT NULL, client_id INT DEFAULT NULL, description LONGTEXT NOT NULL, rdv_valide TINYINT(1) NOT NULL, INDEX IDX_10C31F868BE96D0D (prestations_id), INDEX IDX_10C31F866F1954BE (type_rdv_id), UNIQUE INDEX UNIQ_10C31F8658C54515 (horaire_id), INDEX IDX_10C31F8619EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE type_rdv (id INT AUTO_INCREMENT NOT NULL, type_rdv VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F868BE96D0D FOREIGN KEY (prestations_id) REFERENCES prestations (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F866F1954BE FOREIGN KEY (type_rdv_id) REFERENCES type_rdv (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F8658C54515 FOREIGN KEY (horaire_id) REFERENCES plage_horaire (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F8619EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES clients (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F8619EB6921');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F8658C54515');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F868BE96D0D');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F866F1954BE');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE plage_horaire');
        $this->addSql('DROP TABLE prestations');
        $this->addSql('DROP TABLE rdv');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE type_rdv');
    }
}
