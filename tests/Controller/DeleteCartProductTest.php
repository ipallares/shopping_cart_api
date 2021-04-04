<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\CartProduct;
use App\Tests\Logic\Converter\Traits\AssertCartResponseContent;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteCartProductTest extends WebTestCase
{
    use FixturesTrait;
    use AssertCartResponseContent;

    private AppFixtures $fixtures;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
        parent::setUp();
    }

    public function testDelete_successful(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cartProduct1 = $this->fixtures->getCartProductReference(AppFixtures::CART_PRODUCT1_REFERENCE);
        $this->assertCount(4, $cart->getCartProducts());

        $uri = '/api/v1.0/cart/' . $cart->getid() . '/cartProduct/' . $cartProduct1->getId();
        $this->client->request('DELETE', $uri);

        $this->assertResponseIsSuccessful();

        $responseContentObject = json_decode($this->client->getResponse()->getContent());
        $this->assertCartResponse_expectedFields($responseContentObject);
        $this->assertDeleteSuccessfulCartResponseValues($responseContentObject, $cartProduct1);
        $this->assertCartResponse_matchesCartEntity($responseContentObject);
    }

    public function testDelete_cartMissing(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);

        $uri = '/api/v1.0/cart/' . $cart->getid() . '/cartProduct/' . uuid_create();
        $this->client->request('DELETE', $uri);

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @param object $cartResponse
     * @param CartProduct $cartProduct
     */
    private function assertDeleteSuccessfulCartResponseValues(object $cartResponse, CartProduct $cartProduct): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);

        $this->assertEquals($cart->getId(), $cartResponse->id);
        $this->assertCount(3, $cartResponse->cartProducts);
        $this->assertCartProductRemoved($cartResponse->cartProducts, $cartProduct);
    }

    private function assertCartProductRemoved(array $cartProductObjects, CartProduct $cartProduct)
    {
        $cartProductFound = collect($cartProductObjects)->search(
            function ($cartProductObject) use ($cartProduct) {
                return $cartProductObject->id === $cartProduct->getId();
            }
        );

        $this->assertFalse($cartProductFound);
    }
}
