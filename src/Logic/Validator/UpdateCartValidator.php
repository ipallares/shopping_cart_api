<?php

declare(strict_types=1);

namespace App\Logic\Validator;

use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use DateTime;
use Exception;
use InvalidArgumentException;

class UpdateCartValidator extends CartInputValidator
{
    protected CartRepository $cartRepository;

    public function __construct(
        string $cartInputSchemaV1,
        JsonSchemaValidator $jsonSchemaValidator,
        ProductRepository $productRepository,
        CartRepository $cartRepository
    ) {
        parent::__construct($cartInputSchemaV1, $jsonSchemaValidator, $productRepository);
        $this->cartRepository = $cartRepository;
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

        $this->cartIdExists($jsonObject);
        $this->cartExists($jsonObject->id);
        $this->lastModifiedEqualOrAfterCreationDate($jsonObject);
        $this->allProductsExist($jsonObject);
        $this->allProductsHaveEnoughStock($jsonObject);
    }

    /**
     * @param object $cart
     */
    private function cartIdExists(object $cart): void
    {
        if (!isset($cart->id)) {
            throw new InvalidArgumentException('When creating a Cart no Cart id is allowed');
        }
    }

    public function CartExists(string $id)
    {
        $this->cartRepository->findWithCertainty($id);
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
