<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Tests\Logic\Converter\Traits\AssertCartResponseContent;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use stdClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateCartTest extends WebTestCase
{
    use FixturesTrait;
    use AssertCartResponseContent;

    private const PRODUCT_QUANTITY = 2;
    private AppFixtures $fixtures;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
        parent::setUp();
    }

    public function testUpdateCart_noProducts(): void
    {
        $this->client->request('PUT', '/api/v1.0/cart', [], [], [], $this->getRequestBodyContent_emptyCart());
        $this->assertResponseIsSuccessful();

        $responseContentObject = json_decode($this->client->getResponse()->getContent());

        $this->assertCartResponse_expectedFields($responseContentObject);
        $this->assertEmptyCartResponseValues($responseContentObject);
        $this->assertCartResponse_matchesCartEntity($responseContentObject);
    }

    public function testUpdateCart_withOneProduct(): void
    {
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
        $this->client->request('PUT', '/api/v1.0/cart', [], [], [], $this->getRequestBodyContent_cartWithOneProduct());
        $this->assertResponseIsSuccessful();

        $responseContentObject = json_decode($this->client->getResponse()->getContent());

        $this->assertCartResponse_expectedFields($responseContentObject);
        $this->assertCartWithOneProductResponseValues($responseContentObject);
        $this->assertCartResponse_matchesCartEntity($responseContentObject);
    }

    /**
     * @return string
     */
    private function getRequestBodyContent_emptyCart(): string
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cartObject = new StdClass();
        $cartId = $cart->getId();
        $cartObject->cartProducts = [];
        $cartObject->id = $cartId;

        return json_encode($cartObject);
    }

    /**
     * @return false|string
     */
    private function getRequestBodyContent_cartWithOneProduct()
    {
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);

        $cartObject = new StdClass();
        $cartObject->id = $cart->getId();

        $cartProduct = new StdClass();
        $cartProduct->quantity = 2;
        $cartProduct->productId = $product1->getId();
        $cartObject->cartProducts = [$cartProduct];

        return json_encode($cartObject);
    }

    /**
     * @param object $cartResponse
     */
    private function assertEmptyCartResponseValues(object $cartResponse): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);

        $this->assertEquals($cart->getId(), $cartResponse->id);
        $this->assertEquals(0, $cartResponse->cartPrice);
        $this->assertEmpty($cartResponse->cartProducts);
    }

    /**
     * @param $responseContentObject
     */
    private function assertCartWithOneProductResponseValues($responseContentObject)
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);

        $this->assertEquals($cart->getId(), $responseContentObject->id);
        $expectedCartPrice = self::PRODUCT_QUANTITY*$product1->getPrice() / 100;
        $this->assertEquals($expectedCartPrice, $responseContentObject->cartPrice);
        $this->assertCount(1, $responseContentObject->cartProducts);

        $cartProduct = $responseContentObject->cartProducts[0];
        $this->assertCartProductResponse_matchProductEntity($cartProduct, $product1);
    }
}
