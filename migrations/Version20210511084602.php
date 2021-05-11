<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511084602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, ticket_id INT NOT NULL, content VARCHAR(255) NOT NULL, is_public TINYINT(1) NOT NULL, INDEX IDX_9474526CB03A8386 (created_by_id), INDEX IDX_9474526C700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, assigned_to_id INT DEFAULT NULL, created_by_id INT NOT NULL, status_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, priority VARCHAR(255) NOT NULL, is_escalated TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, date_closed DATETIME NOT NULL, INDEX IDX_97A0ADA3F4BD7827 (assigned_to_id), INDEX IDX_97A0ADA3B03A8386 (created_by_id), INDEX IDX_97A0ADA36BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, number_closed_tickets INT DEFAULT NULL, number_reopened_tickets INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA36BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA36BF700BD');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C700047D2');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB03A8386');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3F4BD7827');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3B03A8386');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE user');
    }
}
