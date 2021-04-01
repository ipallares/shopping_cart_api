<?php

declare(strict_types=1);

namespace App\Logic\Validator;

use App\Repository\ProductRepository;
use InvalidArgumentException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartInputValidator
{
    protected string $inputSchema;
    protected JsonSchemaValidator $jsonSchemaValidator;
    protected ProductRepository $productRepository;

    public function __construct(string $inputSchema, JsonSchemaValidator $jsonSchemaValidator, ProductRepository $productRepository)
    {
        $this->inputSchema = $inputSchema;
        $this->jsonSchemaValidator = $jsonSchemaValidator;
        $this->productRepository = $productRepository;
    }

    /**
     * @param object $jsonObject
     */
    protected function allProductsExist(object $jsonObject): void
    {
        foreach($jsonObject->cartProducts as $cartProduct) {
            $this->productExist($cartProduct->productId);
        }
    }

    /**
     * @param string $productId
     */
    protected function productExist(string $productId): void
    {
        if (null == $this->productRepository->find($productId)) {
            throw new ResourceNotFoundException("Product#$productId doesn't exist");
        }
    }

    /**
     * @param object $jsonObject
     */
    protected function allProductsHaveEnoughStock(object $jsonObject): void
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
    protected function productHasEnoughStock(int $quantity, $productId): void
    {
        $product = $this->productRepository->find($productId);
        if ($quantity > $product->getStock()) {
            throw new InvalidArgumentException("There is not enough stock for product#$productId 
                                                (quantity:$quantity, stock: " . $product->getStock() . ")");
        }
    }
}
