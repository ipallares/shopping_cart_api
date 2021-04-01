<?php

declare(strict_types=1);

namespace App\Tests\Logic\Validator;

use App\DataFixtures\AppFixtures;
use App\Entity\Product;
use App\Logic\Converter\CartEntityToJson;
use App\Logic\Validator\CreateCartValidator;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CreateCartValidatorTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private CreateCartValidator $inputValidator;
    private CartEntityToJson $cartEntityToJson;
    private ProductRepository $productRepository;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->inputValidator = self::$container->get(CreateCartValidator::class);
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
            $cartJson = $this->getNewCartJson();
            $this->inputValidator->validate($cartJson);
        } catch (Exception $e) {
            $valid = false;
        }

        $this->assertTrue($valid);
    }

    public function testValidInput_productQuantityEqualsStock(): void
    {
        $valid = true;
        try {
            $cartJson = $this->getNewCartJson();
            $cartJson = $this->setProductQuantitySameAsStock($cartJson);
            $this->inputValidator->validate($cartJson);
        } catch (Exception $e) {
            $valid = false;
        }

        $this->assertTrue($valid);
    }

    public function testInvalidInput_missingProduct(): void
    {
        $cartJson = $this->getNewCartJson();
        $cartJson = $this->setNonExistingProductId($cartJson);
        $this->expectException(ResourceNotFoundException::class);
        $this->inputValidator->validate($cartJson);
    }

    public function testInvalidInput_notEnoughStock(): void
    {
        $cartJson = $this->getNewCartJson();
        $cartJson = $this->setProductQuantityBiggerThanStock($cartJson);
        $this->expectException(InvalidArgumentException::class);
        $this->inputValidator->validate($cartJson);
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

    private function getNewCartJson(): string
    {
        $newCartObject = $this->getNewEmptyCartObject();
        $quantity1 = 1;
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $newCartObject->cartProducts[] = $this->getCartProductObject($quantity1, $product1);

        return json_encode($newCartObject);
    }

    /**
     * Builds an object for a not yet existing Cart with format expected by the API Request.
     *
     * @return object
     */
    private function getNewEmptyCartObject(): object
    {
        $cartObject = new StdClass();
        $cartObject->cartProducts = [];

        return $cartObject;
    }

    /**
     * Builds an object for a CartProduct based on the quantity and product received as parameters, with format
     * expected by the API Request.
     *
     * @param int $quantity
     * @param Product $product
     *
     * @return object
     */
    private function getCartProductObject(int $quantity, Product $product): object
    {
        $productObject = new StdClass();
        $productObject->quantity = $quantity;
        $productObject->productId = $product->getId();

        return $productObject;
    }
}
