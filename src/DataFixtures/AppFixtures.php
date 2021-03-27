<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const CART_REFERENCE = 'cart';

    public const CART_PRODUCT1_REFERENCE = 'cartProduct1';
    public const CART_PRODUCT2_REFERENCE = 'cartProduct2';
    public const CART_PRODUCT3_REFERENCE = 'cartProduct3';
    public const CART_PRODUCT4_REFERENCE = 'cartProduct4';

    public const PRODUCT1_REFERENCE = 'product1';
    public const PRODUCT2_REFERENCE = 'product2';
    public const PRODUCT3_REFERENCE = 'product3';
    public const PRODUCT4_REFERENCE = 'product4';
    public const PRODUCT5_REFERENCE = 'product5';
    public const PRODUCT6_REFERENCE = 'product6';

    public function load(ObjectManager $manager)
    {
        $this->setReferenceRepository(new ReferenceRepository($manager));

        $cart = new Cart();
        $this->addReference(self::CART_REFERENCE, $cart);
        $manager->persist($cart);

        $product1 = new Product('Product1', 1199, 200);
        $this->addReference(self::PRODUCT1_REFERENCE, $product1);
        $manager->persist($product1);

        $product2 = new Product('Product2', 2499, 10);
        $this->addReference(self::PRODUCT2_REFERENCE, $product2);
        $manager->persist($product2);

        $product3 = new Product('Product3', 1599, 25);
        $this->addReference(self::PRODUCT3_REFERENCE, $product3);
        $manager->persist($product3);

        $product4 = new Product('Product4', 1999, 9);
        $this->addReference(self::PRODUCT4_REFERENCE, $product4);
        $manager->persist($product4);

        $product5 = new Product('Product5', 1799, 25);
        $this->addReference(self::PRODUCT5_REFERENCE, $product5);
        $manager->persist($product5);

        $product6 = new Product('Product6', 2999, 9);
        $this->addReference(self::PRODUCT6_REFERENCE, $product6);
        $manager->persist($product6);

        $cartProduct1 = new CartProduct(1, $cart, $product1);
        $this->addReference(self::CART_PRODUCT1_REFERENCE, $cartProduct1);
        $manager->persist($cartProduct1);

        $cartProduct2 = new CartProduct(1, $cart, $product2);
        $this->addReference(self::CART_PRODUCT2_REFERENCE, $cartProduct2);
        $manager->persist($cartProduct2);

        $cartProduct3 = new CartProduct(2, $cart, $product3);
        $this->addReference(self::CART_PRODUCT3_REFERENCE, $cartProduct3);
        $manager->persist($cartProduct3);

        $cartProduct4 = new CartProduct(1, $cart, $product4);
        $this->addReference(self::CART_PRODUCT4_REFERENCE, $cartProduct4);
        $manager->persist($cartProduct4);

        $cart->addCartProduct($cartProduct1);
        $cart->addCartProduct($cartProduct2);
        $manager->persist($cart);

        $manager->flush();
    }

    public function getCartReference($name): Cart
    {
        /** @var Cart $cart */
        $cart = parent::getReference($name);

        return $cart;
    }

    public function getProductReference($name): Product
    {
        /** @var Product $product */
        $product = parent::getReference($name);

        return $product;
    }

    public function getCartProductReference($name): CartProduct
    {
        /** @var CartProduct $cartProduct */
        $cartProduct = parent::getReference($name);

        return $cartProduct;
    }
}
