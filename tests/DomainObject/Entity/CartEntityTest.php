<?php

declare(strict_types=1);

namespace App\Tests\DomainObject\Entity;

use App\DomainObject\Entity\CartEntity;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

class CartEntityTest extends TestCase
{
    public function testValidData(): void
    {
        $id = uuid_create();
        $creationDate = new DateTime();
        $lastModified = new DateTime();
        $cartProducts = new Collection();

        $cartEntity = new CartEntity(
            $id,
            $creationDate,
            $lastModified,
            $cartProducts
        );

        $this->assertEquals($id, $cartEntity->getId());
        $this->assertEquals($creationDate->getTimestamp(), $cartEntity->getCreationDate()->getTimestamp());
        $this->assertEquals($lastModified->getTimestamp(), $cartEntity->getLastModified()->getTimestamp());
        $this->assertCount(0, $cartEntity->getCartProducts());
    }

    public function testInvalidData_invalidId(): void
    {
        $id = 'invalid-id';
        $creationDate = new DateTime();
        $lastModified = new DateTime();
        $cartProducts = new Collection();

        $this->expectException(InvalidArgumentException::class);

        $cartEntity = new CartEntity(
            $id,
            $creationDate,
            $lastModified,
            $cartProducts
        );
    }

    public function testInvalidData_lastModifiedBeforeCreationDate(): void
    {
        $id = uuid_create();
        $creationDate = new DateTime();
        $lastModified = new DateTime('yesterday');
        $cartProducts = new Collection();

        $this->expectException(InvalidArgumentException::class);

        $cartEntity = new CartEntity(
            $id,
            $creationDate,
            $lastModified,
            $cartProducts
        );
    }
}
