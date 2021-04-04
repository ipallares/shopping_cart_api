<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Logic\Converter\CartEntityToJsonObject;
use App\Repository\CartRepository;

class CartGetter
{
    private CartRepository $cartRepository;
    private CartEntityToJsonObject $cartEntityToJsonObject;

    public function __construct(
        CartRepository $cartRepository,
        CartEntityToJsonObject $cartEntityToJsonObject)
    {
        $this->cartRepository = $cartRepository;
        $this->cartEntityToJsonObject = $cartEntityToJsonObject;
    }

    /**
     * @param string $id
     *
     * @return object
     */
    public function get(string $id): object
    {
        $cart = $this->cartRepository->findWithCertainty($id);

        return $this->cartEntityToJsonObject->convert($cart);
    }
}
