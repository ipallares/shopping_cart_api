<?php

declare(strict_types=1);

namespace App\Tests\Logic\Validator;

use App\DataFixtures\AppFixtures;
use App\Logic\Converter\CartEntityToJson;
use App\Logic\Validator\UpdateCartValidator;
use App\Repository\ProductRepository;
use App\Tests\Logic\Validator\Traits\JsonValidationMethods;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UpdateCartValidatorTest extends KernelTestCase
{
    use JsonValidationMethods;
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private UpdateCartValidator $validator;
    private CartEntityToJson $cartEntityToJson;
    private ProductRepository $productRepository;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->validator = self::$container->get(UpdateCartValidator::class);
        $this->cartEntityToJson = self::$container->get(CartEntityToJson::class);
        $this->productRepository = self::$container->get(ProductRepository::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testValidInput(): void
    {
        $valid = true;
        try {
            $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
            $cartJson = $this->cartEntityToJson->convert($cart);
            $this->validator->validate($cartJson);
        } catch (Exception $e) {
            $valid = false;
        }

        $this->assertTrue($valid);
    }

    public function testValidInput_productQuantityEqualsStock(): void
    {
        $valid = true;
        try {
            $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
            $cartJson = $this->cartEntityToJson->convert($cart);
            $cartJson = $this->setProductQuantitySameAsStock($cartJson);
            $this->validator->validate($cartJson);
        } catch (Exception $e) {
            $valid = false;
        }

        $this->assertTrue($valid);
    }

    public function testInvalidInput_CartNotExisting(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $jsonObject->id = uuid_create();
        $this->expectException(ResourceNotFoundException::class);
        $this->validator->validate(json_encode($jsonObject));
    }

    public function testInvalidInput_missingProduct(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cartJson = $this->cartEntityToJson->convert($cart);
        $cartJson = $this->setNonExistingProductId($cartJson);
        $this->expectException(ResourceNotFoundException::class);
        $this->validator->validate($cartJson);
    }

    public function testInvalidInput_notEnoughStock(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cartJson = $this->cartEntityToJson->convert($cart);
        $cartJson = $this->setProductQuantityBiggerThanStock($cartJson);
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate($cartJson);
    }

    private function setProductQuantitySameAsStock(string $cartJson): string
    {
        $cartObject = json_decode($cartJson);
        $productId = $cartObject->cartProducts[0]->productId;
        $product = $this->productRepository->find($productId);
        $cartObject->cartProducts[0]->quantity = $product->getStock();

        return json_encode($cartObject);
    }

    private function setNonExistingProductId(string $cartJson): string
    {
        $cartObject = json_decode($cartJson);
        $cartObject->cartProducts[0]->productId = uuid_create();

        return json_encode($cartObject);
    }

    private function setProductQuantityBiggerThanStock(string $cartJson): string
    {
        $cartObject = json_decode($cartJson);
        $productId = $cartObject->cartProducts[0]->productId;
        $product = $this->productRepository->find($productId);

        $cartObject->cartProducts[0]->quantity = $product->getStock() + 1;

        return json_encode($cartObject);
    }
}
