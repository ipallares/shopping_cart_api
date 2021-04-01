<?php

declare(strict_types=1);

namespace App\Logic\Validator;

use App\Repository\ProductRepository;
use Exception;
use InvalidArgumentException;

class CreateCartValidator extends CartInputValidator
{
    public function __construct(
        string $createCartInputSchemaV1,
        JsonSchemaValidator $jsonSchemaValidator,
        ProductRepository $productRepository
    ) {
        parent::__construct($createCartInputSchemaV1, $jsonSchemaValidator, $productRepository);
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
        // noCartId, noCreationDate and noLastModified should be covered by having different schemas for creation and update
        $this->noCartId($jsonObject);
        $this->noCreationDate($jsonObject);
        $this->noLastModified($jsonObject);
        $this->allProductsExist($jsonObject);
        $this->allProductsHaveEnoughStock($jsonObject);
    }

    /**
     * @param object $cart
     */
    private function noCartId(object $cart): void
    {
        if (isset($cart->id)) {
            throw new InvalidArgumentException('When creating a Cart no Cart id is allowed');
        }
    }

    /**
     * @param object $cart
     */
    private function noCreationDate(object $cart): void
    {
        if (isset($cart->creationDate)) {
            throw new InvalidArgumentException('When creating a Cart no creationDate is allowed');
        }
    }

    /**
     * @param object $cart
     */
    private function noLastModified(object $cart): void
    {
        if (isset($cart->lastModified)) {
            throw new InvalidArgumentException('When creating a Cart no lastModified date is allowed');
        }
    }
}
