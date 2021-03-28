<?php

declare(strict_types=1);

namespace App\UseCase\CartProduct;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class CartProductCreator
{
    private CartProductRepository $cartProductRepository;
    private ProductRepository $productRepository;
    private CartRepository $cartRepository;

    public function __construct(CartProductRepository $cartProductRepository, ProductRepository $productRepository, CartRepository $cartRepository)
    {
        $this->cartProductRepository = $cartProductRepository;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param int $quantity
     * @param string $cartId
     * @param string $productId
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(int $quantity, string $cartId, string $productId)
    {
        $cart = $this->cartRepository->findWithCertainty($cartId);
        $product = $this->productRepository->findWithCertainty($productId);
        $existingCartProduct = $this->getProductFromCart($cart, $productId);

        if (null === $existingCartProduct) {
            $this->addNewCartProduct($quantity, $cart, $product);
        } else {
            $this->mergeWithExistingCartProduct($quantity, $cart, $product, $existingCartProduct);
        }
    }

    /**
     * @param int $quantity
     * @param Cart $cart
     * @param Product $product
     * @param CartProduct $existingCartProduct
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function mergeWithExistingCartProduct(
        int $quantity,
        Cart $cart,
        Product $product,
        CartProduct $existingCartProduct
    ): void {
        $existingQuantity = $existingCartProduct->getQuantity();
        $totalQuantity = $quantity + $existingQuantity;
        $this->cartProductRepository->remove($existingCartProduct);
        $this->addNewCartProduct($totalQuantity, $cart, $product);
    }

    /**
     * @param int $quantity
     * @param Cart $cart
     * @param Product $product
     *
     * @throws ORMException
     */
    private function addNewCartProduct(int $quantity, Cart $cart, Product $product): void
    {
        $cartProduct = new CartProduct($quantity, $cart, $product);
        $cart->addCartProduct($cartProduct);
        $this->cartRepository->save($cart);
    }

    /**
     * @param Cart $cart
     * @param string $productId
     *
     * @return CartProduct|null
     */
    private function getProductFromCart(Cart $cart, string $productId): ?CartProduct
    {
        foreach($cart->getCartProducts() as $cartProduct) {
            if ($cartProduct->getProductId() === $productId) {
                return $cartProduct;
            }
        }

        return null;
    }
}
