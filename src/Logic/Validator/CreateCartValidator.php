<?php

declare(strict_types=1);

namespace App\Logic\Validator;

use App\Repository\ProductRepository;
use Exception;

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
        $this->allProductsExist($jsonObject);
        $this->allProductsHaveEnoughStock($jsonObject);
    }
}
