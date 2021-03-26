<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210326182423 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Change id to type from integer to string and minor refactors to "cart", "cart_product" and "product" tables.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__cart AS SELECT id, creation_date, last_modified FROM cart');
        $this->addSql('DROP TABLE cart');
        $this->addSql('CREATE TABLE cart (
                            id VARCHAR(36) NOT NULL, 
                            creation_date DATE NOT NULL, 
                            last_modified DATE NOT NULL, 
                            PRIMARY KEY(id)
                    )'
        );
        $this->addSql('INSERT INTO cart (id, creation_date, last_modified) SELECT id, creation_date, last_modified FROM __temp__cart');
        $this->addSql('DROP TABLE __temp__cart');
        $this->addSql('DROP INDEX IDX_2890CCAA4584665A');
        $this->addSql('DROP INDEX IDX_2890CCAA1AD5CDBF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cart_product AS SELECT id, cart_id, product_id, quantity FROM cart_product');
        $this->addSql('DROP TABLE cart_product');
        $this->addSql('CREATE TABLE cart_product (
                            id VARCHAR(36) NOT NULL, 
                            cart_id VARCHAR(36) NOT NULL, 
                            product_id VARCHAR(36) NOT NULL, 
                            quantity INTEGER NOT NULL, 
                            PRIMARY KEY(id), 
                            CONSTRAINT FK_2890CCAA1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE, 
                            CONSTRAINT FK_2890CCAA4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
                            )'
        );
        $this->addSql('INSERT INTO cart_product (id, cart_id, product_id, quantity) SELECT id, cart_id, product_id, quantity FROM __temp__cart_product');
        $this->addSql('DROP TABLE __temp__cart_product');
        $this->addSql('CREATE INDEX IDX_2890CCAA4584665A ON cart_product (product_id)');
        $this->addSql('CREATE INDEX IDX_2890CCAA1AD5CDBF ON cart_product (cart_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, price, stock FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (
                            id VARCHAR(36) NOT NULL, 
                            name VARCHAR(255) NOT NULL COLLATE BINARY, 
                            price INTEGER NOT NULL, 
                            stock INTEGER NOT NULL, 
                            PRIMARY KEY(id)
                        )'
        );
        $this->addSql('INSERT INTO product (id, name, price, stock) SELECT id, name, price, stock FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__cart AS SELECT id, creation_date, last_modified FROM cart');
        $this->addSql('DROP TABLE cart');
        $this->addSql('CREATE TABLE cart (
                            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                            creation_date DATE NOT NULL, 
                            last_modified DATE NOT NULL
                            )'
        );
        $this->addSql('INSERT INTO cart (id, creation_date, last_modified) SELECT id, creation_date, last_modified FROM __temp__cart');
        $this->addSql('DROP TABLE __temp__cart');
        $this->addSql('DROP INDEX IDX_2890CCAA1AD5CDBF');
        $this->addSql('DROP INDEX IDX_2890CCAA4584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cart_product AS SELECT id, cart_id, product_id, quantity FROM cart_product');
        $this->addSql('DROP TABLE cart_product');
        $this->addSql('CREATE TABLE cart_product (
                            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                            quantity INTEGER NOT NULL, 
                            cart_id INTEGER NOT NULL, 
                            product_id INTEGER NOT NULL
                        )'
        );
        $this->addSql('INSERT INTO cart_product (id, cart_id, product_id, quantity) SELECT id, cart_id, product_id, quantity FROM __temp__cart_product');
        $this->addSql('DROP TABLE __temp__cart_product');
        $this->addSql('CREATE INDEX IDX_2890CCAA1AD5CDBF ON cart_product (cart_id)');
        $this->addSql('CREATE INDEX IDX_2890CCAA4584665A ON cart_product (product_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, price, stock FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (
                            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                            name VARCHAR(255) NOT NULL, 
                            price INTEGER NOT NULL, 
                            stock INTEGER NOT NULL)');
        $this->addSql('INSERT INTO product (id, name, price, stock) SELECT id, name, price, stock FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }
}
