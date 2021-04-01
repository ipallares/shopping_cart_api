<?php

declare(strict_types=1);

namespace App\Logic\Converter;

use App\Entity\Cart;
use App\Entity\CartProduct;
use Doctrine\Common\Collections\Collection;
use stdClass;

class CartEntityToJson
{
    public function convert(Cart $cartEntity): string
    {
        $cartObject = new StdClass();
        $cartObject->id = $cartEntity->getId();
        $cartObject->creationDate = $cartEntity->getCreationDate()->format('d.m.Y H:i:s');
        $cartObject->lastModified = $cartEntity->getLastModified()->format('d.m.Y H:i:s');
        $cartObject->cartPrice = $this->getCartPrice($cartEntity);
        $cartObject->cartProducts = $this->convertCartProducts($cartEntity->getCartProducts());

        return json_encode($cartObject);
    }

    private function convertCartProducts(Collection $cartProducts): array
    {
        $result = [];
        foreach($cartProducts as $cartProduct) {
            $result[] = $this->convertCartProduct($cartProduct);
        }

        return $result;
    }

    private function convertCartProduct(CartProduct $cartProductEntity): object
    {
        $cartProductObject = new stdClass();
        $cartProductObject->id = $cartProductEntity->getId();
        $cartProductObject->quantity = $cartProductEntity->getQuantity();
        $cartProductObject->productName = $cartProductEntity->getProductName();
        $cartProductObject->productPrice = $cartProductEntity->getProductPrice() / 100;
        $cartProductObject->productStock = $cartProductEntity->getProductStock();
        $cartProductObject->productId = $cartProductEntity->getProductId();
        $cartProductObject->cartProductPrice = $cartProductEntity->getCartProductPrice() / 100;

        return $cartProductObject;
    }

    private function getCartPrice(Cart $cartEntity): float
    {
        $price = 0;
        foreach($cartEntity->getCartProducts() as $cartProduct) {
            $price += $cartProduct->getCartProductPrice();
        }

        return $price;
    }
}
