<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Logic\Converter\CartEntityToJsonObject;
use App\Logic\Converter\JsonToCartEntity;
use App\Logic\Validator\CreateCartValidator;
use App\Repository\CartRepository;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use JsonSchema\Exception\InvalidSchemaException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartCreator
{
    private CartRepository $cartRepository;
    private CreateCartValidator $inputValidator;
    private JsonToCartEntity $jsonToCartEntity;
    private CartEntityToJsonObject $cartEntityToJsonObject;

    public function __construct(
        CartRepository $cartRepository,
        CreateCartValidator $inputValidator,
        JsonToCartEntity $jsonToCartEntity,
        CartEntityToJsonObject $cartEntityToJsonObject)
    {
        $this->cartRepository = $cartRepository;
        $this->inputValidator = $inputValidator;
        $this->jsonToCartEntity = $jsonToCartEntity;
        $this->cartEntityToJsonObject = $cartEntityToJsonObject;
    }

    /**
     * @param string $cartJson
     *
     * @return object
     *
     * @throws ORMException
     * @throws InvalidArgumentException
     * @throws InvalidSchemaException
     * @throws ResourceNotFoundException
     * @throws Exception
     */
    public function create(string $cartJson): object
    {
        $this->inputValidator->validate($cartJson);
        $cart = $this->jsonToCartEntity->convert($cartJson);
        $this->cartRepository->save($cart);

        return $this->cartEntityToJsonObject->convert($cart);
    }
}
