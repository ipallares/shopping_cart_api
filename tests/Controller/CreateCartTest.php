<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Tests\Logic\Converter\Traits\AssertCartResponseContent;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use stdClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateCartTest extends WebTestCase
{
    private KernelBrowser $client;

    use FixturesTrait;
    use AssertCartResponseContent;

    private const PRODUCT_QUANTITY = 2;

    private AppFixtures $fixtures;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
        parent::setUp();
    }

    public function testCreateCart_noProducts(): void
    {
        $this->client->request('POST', '/api/v1.0/cart', [], [], [], $this->getRequestBodyContent_emptyCart());
        $this->assertResponseIsSuccessful();

        $responseContentObject = json_decode($this->client->getResponse()->getContent());

        $this->assertCartResponse_expectedFields($responseContentObject);
        $this->assertEmptyCartResponseValues($responseContentObject);
        $this->assertCartResponse_matchesCartEntity($responseContentObject);
    }

    public function testCreateCart_withOneProduct(): void
    {
        $this->client->request('POST', '/api/v1.0/cart', [], [], [], $this->getRequestBodyContent_cartWithOneProduct());
        $this->assertResponseIsSuccessful();

        $responseContentObject = json_decode($this->client->getResponse()->getContent());

        $this->assertCartResponse_expectedFields($responseContentObject);
        $this->assertCartWithOneProductResponseValues($responseContentObject);
        $this->assertCartResponse_matchesCartEntity($responseContentObject);
    }

    private function getRequestBodyContent_emptyCart(): string
    {
        $cartObject = new StdClass();
        $cartObject->cartProducts = [];

        return json_encode($cartObject);
    }

    private function getRequestBodyContent_cartWithOneProduct()
    {
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);

        $cartObject = new StdClass();
        $cartProduct = new StdClass();
        $cartProduct->quantity = 2;
        $cartProduct->productId = $product1->getId();
        $cartObject->cartProducts = [$cartProduct];

        return json_encode($cartObject);
    }

    private function assertEmptyCartResponseValues(object $cartResponse): void
    {
        $this->assertEquals(0, $cartResponse->cartPrice);
        $this->assertEmpty($cartResponse->cartProducts);
    }

    private function assertCartWithOneProductResponseValues($responseContentObject)
    {
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);

        $this->assertEquals(self::PRODUCT_QUANTITY*$product1->getPrice() / 100, $responseContentObject->cartPrice);
        $this->assertCount(1, $responseContentObject->cartProducts);

        $cartProduct = $responseContentObject->cartProducts[0];
        $this->assertCartProductResponse_matchProductEntity($cartProduct, $product1);
    }
}
