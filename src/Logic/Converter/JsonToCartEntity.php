<?php

declare(strict_types=1);

namespace App\Logic\Converter;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\ORMException;
use Exception;

class JsonToCartEntity
{
    private CartRepository $cartRepository;
    private CartProductRepository $cartProductRepository;
    private ProductRepository $productRepository;

    public function __construct(
        CartRepository $cartRepository,
        CartProductRepository $cartProductRepository,
        ProductRepository $productRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->cartProductRepository = $cartProductRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $json
     *
     * @return Cart
     *
     * @throws ORMException
     * @throws Exception
     */
    public function convert(string $json): Cart
    {
        $jsonObject = json_decode($json);
        $cart = $this->getCart($jsonObject);
        $cartProducts = $this->convertCartProducts($cart, $jsonObject->cartProducts);

        $cart->setCartProducts($cartProducts);

        $this->cartRepository->save($cart);

        return $cart;
    }

    /**
     * @param object $jsonObject
     *
     * @return Cart
     *
     * @throws Exception
     */
    private function getCart(object $jsonObject): Cart
    {
        if (isset($jsonObject->id)) {
            $cart = $this->cartRepository->find($jsonObject->id);
            $cart->setLastModified(new DateTime());
        } else {
            $cart = new Cart();
        }

        return $cart;
    }

    /**
     * @param Cart $cart
     * @param array $cartProductObjects
     *
     * @return Collection
     */
    private function convertCartProducts(Cart $cart, array $cartProductObjects): Collection
    {
        $result = new ArrayCollection();
        foreach($cartProductObjects as $cartProductObject) {
            $result[] = $this->convertCartProduct($cart, $cartProductObject);
        }

        return $result;
    }

    /**
     * @param Cart $cart
     * @param $cartProductObject
     *
     * @return CartProduct
     */
    private function convertCartProduct(Cart $cart, $cartProductObject): CartProduct
    {
        $product = $this->productRepository->find($cartProductObject->productId);

        return new CartProduct($cartProductObject->quantity, $cart, $product);
    }
}
