<?php

declare(strict_types=1);

namespace App\Logic\Validator\CartProduct;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use InvalidArgumentException;

class CreateCartProductValidator
{
    private CartRepository $cartRepository;
    private ProductRepository $productRepository;

    /**
     * @param CartRepository $cartRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(CartRepository $cartRepository, ProductRepository $productRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    public function validate(int $quantity, string $cartId, string $productId)
    {
        $cart = $this->cartRepository->findWithCertainty($cartId);
        $product = $this->productRepository->findWithCertainty($productId);
        $existingCartProduct = $this->getProductFromCart($cart, $productId);
        $requiredStock = null === $existingCartProduct
            ? $quantity
            : $quantity + $existingCartProduct->getQuantity();

        if ($product->getStock() < $requiredStock) {
            throw new InvalidArgumentException("There is not enough stock for product#$productId 
                                                (quantity:$requiredStock, stock: " . $product->getStock() . ")");
        }

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
