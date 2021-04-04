<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Logic\Converter\CartEntityToJsonObject;
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
    private CartEntityToJsonObject $cartEntityToJsonObject;

    public function __construct(
        CartRepository $cartRepository,
        UpdateCartValidator $validator,
        JsonToCartEntity $jsonToCartEntity,
        CartEntityToJsonObject $cartEntityToJsonObject)
    {
        $this->cartRepository = $cartRepository;
        $this->validator = $validator;
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
    public function update(string $cartJson): object
    {
        $this->validator->validate($cartJson);
        $cart = $this->jsonToCartEntity->convert($cartJson);
        $this->cartRepository->save($cart);

        return $this->cartEntityToJsonObject->convert($cart);
    }
}
