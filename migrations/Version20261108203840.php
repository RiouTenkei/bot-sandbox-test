<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261108203840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id VARCHAR(32) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE items (id VARCHAR(32) NOT NULL, name VARCHAR(255) NOT NULL, price_minor INT NOT NULL, currency VARCHAR(3) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, category_id VARCHAR(32) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_E11EE94D12469DE2 ON items (category_id)');
        $this->addSql('ALTER TABLE items ADD CONSTRAINT FK_E11EE94D12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE');

        $this->addSql("INSERT INTO categories (id, name) VALUES ('workshop', 'Workshop')");
        $this->addSql("INSERT INTO categories (id, name) VALUES ('writing', 'Writing')");
        $this->addSql("INSERT INTO categories (id, name) VALUES ('garden', 'Garden')");

        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-01', 'Anvil', 500, 'GBP', '2026-01-01T09:00:00+00:00', 'workshop')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-02', 'Bellows', 625, 'GBP', '2026-01-02T09:00:00+00:00', 'workshop')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-03', 'Chisel', 750, 'GBP', '2026-01-03T09:00:00+00:00', 'workshop')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-04', 'Drill', 875, 'GBP', '2026-01-04T09:00:00+00:00', 'workshop')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-05', 'Easel', 1000, 'GBP', '2026-01-05T09:00:00+00:00', 'workshop')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-06', 'File', 1125, 'GBP', '2026-01-06T09:00:00+00:00', 'workshop')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-07', 'Gouge', 1250, 'GBP', '2026-01-07T09:00:00+00:00', 'workshop')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-08', 'Hammer', 1375, 'GBP', '2026-01-08T09:00:00+00:00', 'workshop')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-09', 'Inkwell', 1500, 'GBP', '2026-01-09T09:00:00+00:00', 'writing')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-10', 'Jigsaw', 1625, 'GBP', '2026-01-10T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-11', 'Kettle', 1750, 'GBP', '2026-01-11T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-12', 'Ladder', 1875, 'GBP', '2026-01-12T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-13', 'Mallet', 2000, 'GBP', '2026-01-13T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-14', 'Needle', 2125, 'GBP', '2026-01-14T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-15', 'Oilcan', 2250, 'GBP', '2026-01-15T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-16', 'Plane', 2375, 'GBP', '2026-01-16T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-17', 'Quill', 2500, 'GBP', '2026-01-17T09:00:00+00:00', 'writing')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-18', 'Rasp', 2625, 'GBP', '2026-01-18T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-19', 'Saw', 2750, 'GBP', '2026-01-19T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-20', 'Trowel', 2875, 'GBP', '2026-01-20T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-21', 'Umbrella', 3000, 'GBP', '2026-01-21T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-22', 'Vice', 3125, 'GBP', '2026-01-22T09:00:00+00:00', 'garden')");
        $this->addSql("INSERT INTO items (id, name, price_minor, currency, created_at, category_id) VALUES ('item-23', 'Wrench', 3250, 'GBP', '2026-01-23T09:00:00+00:00', 'garden')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE items DROP CONSTRAINT FK_E11EE94D12469DE2');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE items');
    }
}
