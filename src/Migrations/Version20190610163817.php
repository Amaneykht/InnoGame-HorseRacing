<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190610163817 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE horse (id INT AUTO_INCREMENT NOT NULL, speed DOUBLE PRECISION NOT NULL, endurance DOUBLE PRECISION NOT NULL, strength DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, created_date_time DATETIME NOT NULL, maximum_distance INT DEFAULT NULL, max_number_of_horses INT DEFAULT NULL, status INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE horse_in_race (id INT AUTO_INCREMENT NOT NULL, race_id INT NOT NULL, horse_id INT NOT NULL, position INT DEFAULT NULL, completed_time TIME DEFAULT NULL, distance_covered DOUBLE PRECISION DEFAULT NULL, INDEX IDX_4D3817556E59D40D (race_id), UNIQUE INDEX UNIQ_4D38175576B275AD (horse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE horse_in_race ADD CONSTRAINT FK_4D3817556E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE horse_in_race ADD CONSTRAINT FK_4D38175576B275AD FOREIGN KEY (horse_id) REFERENCES horse (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE horse_in_race DROP FOREIGN KEY FK_4D38175576B275AD');
        $this->addSql('ALTER TABLE horse_in_race DROP FOREIGN KEY FK_4D3817556E59D40D');
        $this->addSql('DROP TABLE horse');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE horse_in_race');
    }
}
