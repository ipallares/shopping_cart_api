<?php

declare(strict_types=1);

namespace App\Tests\UseCase;

use App\DataFixtures\AppFixtures;
use App\UseCase\CartGetter;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartGetterTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private CartGetter $cartGetter;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartGetter = self::$container->get(CartGetter::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testGet_existingCart(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cartFoundObject = $this->cartGetter->get($cart->getId());

        $this->assertEquals($cart->getId(), $cartFoundObject->id);
        $this->assertEquals($cart->getNumberOfProducts(), count($cartFoundObject->cartProducts));

        // IPT: TODO: More detailed tests should be done here
    }

    public function testGet_nonExistingCart(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $cartFoundJson = $this->cartGetter->get(uuid_create());
    }
}
