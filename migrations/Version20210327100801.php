<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210327100801 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Removes the relation between a CartProduct and a Cart (Cart keeps being related to CartProduct).';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('DROP INDEX IDX_2890CCAA1AD5CDBF');
        $this->addSql('DROP INDEX IDX_2890CCAA4584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cart_product AS SELECT id, product_id, quantity FROM cart_product');
        $this->addSql('DROP TABLE cart_product');
        $this->addSql('CREATE TABLE cart_product (id VARCHAR(36) NOT NULL COLLATE BINARY, product_id VARCHAR(36) NOT NULL COLLATE BINARY, quantity INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2890CCAA4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cart_product (id, product_id, quantity) SELECT id, product_id, quantity FROM __temp__cart_product');
        $this->addSql('DROP TABLE __temp__cart_product');
        $this->addSql('CREATE INDEX IDX_2890CCAA4584665A ON cart_product (product_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP INDEX IDX_2890CCAA4584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cart_product AS SELECT id, product_id, quantity FROM cart_product');
        $this->addSql('DROP TABLE cart_product');
        $this->addSql('CREATE TABLE cart_product (id VARCHAR(36) NOT NULL, product_id VARCHAR(36) NOT NULL, quantity INTEGER NOT NULL, cart_id VARCHAR(36) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO cart_product (id, product_id, quantity) SELECT id, product_id, quantity FROM __temp__cart_product');
        $this->addSql('DROP TABLE __temp__cart_product');
        $this->addSql('CREATE INDEX IDX_2890CCAA4584665A ON cart_product (product_id)');
        $this->addSql('CREATE INDEX IDX_2890CCAA1AD5CDBF ON cart_product (cart_id)');
    }
}
