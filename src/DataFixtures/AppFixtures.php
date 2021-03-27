<?php

namespace App\DataFixtures;

use App\Entity\CartDE;
use App\Entity\CartProductDE;
use App\Entity\ProductDE;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const CART_REFERENCE = 'cart';

    public const CART_PRODUCT1_REFERENCE = 'cartProduct1';
    public const CART_PRODUCT2_REFERENCE = 'cartProduct2';

    public const PRODUCT1_REFERENCE = 'product1';
    public const PRODUCT2_REFERENCE = 'product2';
    public const PRODUCT3_REFERENCE = 'product3';
    public const PRODUCT4_REFERENCE = 'product4';

    public function load(ObjectManager $manager)
    {
        $this->setReferenceRepository(new ReferenceRepository($manager));

        $product1 = new ProductDE('Product1', 1199, 200);
        $this->addReference(self::PRODUCT1_REFERENCE, $product1);
        $manager->persist($product1);

        $product2 = new ProductDE('Product2', 2499, 10);
        $this->addReference(self::PRODUCT2_REFERENCE, $product2);
        $manager->persist($product2);

        $product3 = new ProductDE('Product3', 1599, 25);
        $this->addReference(self::PRODUCT3_REFERENCE, $product3);
        $manager->persist($product3);

        $product4 = new ProductDE('Product4', 1999, 9);
        $this->addReference(self::PRODUCT4_REFERENCE, $product4);
        $manager->persist($product4);

        $cartProduct1 = new CartProductDE(1, $product1);
        $this->addReference(self::CART_PRODUCT1_REFERENCE, $cartProduct1);
        $manager->persist($cartProduct1);

        $cartProduct2 = new CartProductDE(1, $product2);
        $this->addReference(self::CART_PRODUCT2_REFERENCE, $cartProduct2);
        $manager->persist($cartProduct2);

        $cart = new CartDE();
        $cart->addCartProduct($cartProduct1);
        $cart->addCartProduct($cartProduct2);
        $this->addReference(self::CART_REFERENCE, $cart);
        $manager->persist($cart);

        $manager->flush();
    }

    public function getCartReference($name): CartDE
    {
        /** @var CartDE $cart */
        $cart = parent::getReference($name);

        return $cart;
    }

    public function getProductReference($name): ProductDE
    {
        /** @var ProductDE $product */
        $product = parent::getReference($name);

        return $product;
    }

    public function getCartProductReference($name): CartProductDE
    {
        /** @var CartProductDE $cartProduct */
        $cartProduct = parent::getReference($name);

        return $cartProduct;
    }
}
