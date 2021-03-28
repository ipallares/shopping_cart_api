<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Logic\Converter\CartEntityToJson;
use App\Repository\CartRepository;

class CartGetter
{
    private CartRepository $cartRepository;
    private CartEntityToJson $cartEntityToJson;

    public function __construct(
        CartRepository $cartRepository,
        CartEntityToJson $cartEntityToJson)
    {
        $this->cartRepository = $cartRepository;
        $this->cartEntityToJson = $cartEntityToJson;
    }

    public function get(string $id): string
    {
        $cart = $this->cartRepository->findWithCertainty($id);

        return $this->cartEntityToJson->convert($cart);
    }
}
