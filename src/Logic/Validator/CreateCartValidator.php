<?php

declare(strict_types=1);

namespace App\Logic\Validator;

use App\Repository\ProductRepository;
use DateTime;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CreateCartValidator
{
    public string $inputSchema;
    public JsonSchemaValidator $jsonSchemaValidator;
    public ProductRepository $productRepository;

    public function __construct(string $cartInputSchemaV1, JsonSchemaValidator $jsonSchemaValidator, ProductRepository $productRepository)
    {
        $this->inputSchema = $cartInputSchemaV1;
        $this->jsonSchemaValidator = $jsonSchemaValidator;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $cartJson
     *
     * @throws Exception
     */
    public function validate(string $cartJson): void
    {
        $this->jsonSchemaValidator->validate($cartJson, $this->inputSchema);
        $jsonObject = json_decode($cartJson);
        $this->lastModifiedEqualOrAfterCreationDate($jsonObject);
        $this->allProductsExist($jsonObject);
        $this->allProductsHaveEnoughStock($jsonObject);
    }

    /**
     * @param object $jsonObject
     */
    private function allProductsExist(object $jsonObject): void
    {
        foreach($jsonObject->cartProducts as $cartProduct) {
            $this->productExist($cartProduct->productId);
        }
    }

    /**
     * @param string $productId
     */
    private function productExist(string $productId): void
    {
        if (null == $this->productRepository->find($productId)) {
            throw new ResourceNotFoundException("Product#$productId");
        }
    }

    /**
     * @param object $jsonObject
     */
    private function allProductsHaveEnoughStock(object $jsonObject): void
    {
        foreach($jsonObject->cartProducts as $cartProduct) {
            $this->productHasEnoughStock($cartProduct->quantity, $cartProduct->productId);
        }
    }

    /**
     * @param int $quantity
     * @param $productId
     *
     * @throws InvalidArgumentException
     * @throws ResourceNotFoundException
     */
    private function productHasEnoughStock(int $quantity, $productId): void
    {
        $product = $this->productRepository->find($productId);
        if ($quantity > $product->getStock()) {
            throw new InvalidArgumentException("There is not enough stock for product#$productId 
                                                (quantity:$quantity, stock: " . $product->getStock() . ")");
        }
    }

    /**
     * @param object $cart
     *
     * @throws Exception
     */
    private function lastModifiedEqualOrAfterCreationDate(object $cart): void
    {
        if (isset($cart->creationDate) && isset($cart->lastModified)) {
            $creationDate = new DateTime($cart->creationDate);
            $lastModified = new DateTime($cart->lastModified);
            if ($creationDate->getTimestamp() > $lastModified->getTimestamp()) {
                throw new InvalidArgumentException('Last modified date can not be previous to creation date');
            }
        }
    }

}
