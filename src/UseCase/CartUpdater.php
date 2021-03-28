<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Logic\Converter\CartEntityToJson;
use App\Logic\Converter\JsonToCartEntity;
use App\Logic\Validator\UpdateCartValidator;
use App\Repository\CartRepository;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use JsonSchema\Exception\InvalidSchemaException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartUpdater
{
    private CartRepository $cartRepository;
    private UpdateCartValidator $validator;
    private JsonToCartEntity $jsonToCartEntity;
    private CartEntityToJson $cartEntityToJson;

    public function __construct(
        CartRepository $cartRepository,
        UpdateCartValidator $validator,
        JsonToCartEntity $jsonToCartEntity,
        CartEntityToJson $cartEntityToJson)
    {
        $this->cartRepository = $cartRepository;
        $this->validator = $validator;
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
    public function update(string $cartJson): string
    {
        $this->validator->validate($cartJson);
        $cart = $this->jsonToCartEntity->convert($cartJson);
        $this->cartRepository->save($cart);

        return $this->cartEntityToJson->convert($cart);
    }
}
