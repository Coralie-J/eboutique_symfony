<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220202201616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, id_panier_id INT DEFAULT NULL, date DATE NOT NULL, UNIQUE INDEX UNIQ_6EEAA67D77482E5B (id_panier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, id_produit_id INT NOT NULL, source VARCHAR(255) NOT NULL, alt VARCHAR(60) NOT NULL, INDEX IDX_6A2CA10CAABEFE2C (id_produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, INDEX IDX_24CC0DF279F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier_line (id INT AUTO_INCREMENT NOT NULL, id_produit_id INT NOT NULL, id_panier_id INT DEFAULT NULL, quantite INT NOT NULL, INDEX IDX_25F2918FAABEFE2C (id_produit_id), INDEX IDX_25F2918F77482E5B (id_panier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, type_categorie_id INT NOT NULL, nom VARCHAR(60) NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, disponibilite TINYINT(1) NOT NULL, INDEX IDX_29A5EC273BB65D28 (type_categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, login VARCHAR(60) NOT NULL, password VARCHAR(50) NOT NULL, nom VARCHAR(60) NOT NULL, prenom VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D77482E5B FOREIGN KEY (id_panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CAABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF279F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier_line ADD CONSTRAINT FK_25F2918FAABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE panier_line ADD CONSTRAINT FK_25F2918F77482E5B FOREIGN KEY (id_panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC273BB65D28 FOREIGN KEY (type_categorie_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC273BB65D28');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D77482E5B');
        $this->addSql('ALTER TABLE panier_line DROP FOREIGN KEY FK_25F2918F77482E5B');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CAABEFE2C');
        $this->addSql('ALTER TABLE panier_line DROP FOREIGN KEY FK_25F2918FAABEFE2C');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF279F37AE5');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_line');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE user');
    }
}
