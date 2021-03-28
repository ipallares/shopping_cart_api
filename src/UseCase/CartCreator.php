<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Logic\Converter\CartEntityToJson;
use App\Logic\Converter\JsonToCartEntity;
use App\Logic\Validator\InputValidator;
use App\Repository\CartRepository;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use JsonSchema\Exception\InvalidSchemaException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartCreator
{
    private CartRepository $cartRepository;
    private InputValidator $inputValidator;
    private JsonToCartEntity $jsonToCartEntity;
    private CartEntityToJson $cartEntityToJson;

    public function __construct(
        CartRepository $cartRepository,
        InputValidator $inputValidator,
        JsonToCartEntity $jsonToCartEntity,
        CartEntityToJson $cartEntityToJson)
    {
        $this->cartRepository = $cartRepository;
        $this->inputValidator = $inputValidator;
        $this->jsonToCartEntity = $jsonToCartEntity;
        $this->cartEntityToJson = $cartEntityToJson;
    }

    /**
     * @param string $cartJson
     *
     * @return string
     *
     * @throws ORMException
     * @throws InvalidArgumentException
     * @throws InvalidSchemaException
     * @throws ResourceNotFoundException
     * @throws Exception
     */
    public function create(string $cartJson): string
    {
        $this->inputValidator->validate($cartJson);
        $cart = $this->jsonToCartEntity->convert($cartJson);
        $this->cartRepository->save($cart);

        return $this->cartEntityToJson->convert($cart);
    }
}
