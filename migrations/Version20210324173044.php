<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210324173044 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add table to store the Products in a Cart';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE cart_product (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                cart_id INTEGER NOT NULL, 
                product_id INTEGER NOT NULL, 
                quantity INTEGER NOT NULL
            )'
        );
        $this->addSql('CREATE INDEX IDX_2890CCAA1AD5CDBF ON cart_product (cart_id)');
        $this->addSql('CREATE INDEX IDX_2890CCAA4584665A ON cart_product (product_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE cart_product');
    }
}
