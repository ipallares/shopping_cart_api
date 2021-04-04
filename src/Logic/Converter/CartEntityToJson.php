<?php

declare(strict_types=1);

namespace App\Logic\Converter;

use App\Entity\Cart;

class CartEntityToJson
{
    private CartEntityToJsonObject $cartEntityToJsonObject;

    public function __construct(CartEntityToJsonObject $cartEntityToJsonObject)
    {
        $this->cartEntityToJsonObject = $cartEntityToJsonObject;
    }

    public function convert(Cart $cartEntity): string
    {
        return json_encode($this->cartEntityToJsonObject->convert($cartEntity));
    }
}
