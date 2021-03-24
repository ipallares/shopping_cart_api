<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210324172201 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add table to store the Carts';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE cart (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                creation_date DATE NOT NULL, 
                last_modified DATE NOT NULL
            )'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE cart');
    }
}
