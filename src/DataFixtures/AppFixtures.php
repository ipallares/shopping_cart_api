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

    public function load(ObjectManager $manager)
    {
        $this->setReferenceRepository(new ReferenceRepository($manager));

        $cart = new CartDE();
        $manager->persist($cart);
        $this->addReference(self::CART_REFERENCE, $cart);

        $product1 = new ProductDE('Product1', 1199, 200);
        $manager->persist($product1);
        $this->addReference('product1', $product1);

        $product2 = new ProductDE('Product2', 2499, 10);
        $manager->persist($product2);
        $this->addReference('product2', $product2);

        $product3 = new ProductDE('Product3', 1599, 25);
        $manager->persist($product3);
        $this->addReference('product3', $product3);

        $product4 = new ProductDE('Product4', 1999, 9);
        $manager->persist($product4);
        $this->addReference('product4', $product4);

        $cartProduct1 = new CartProductDE(1, $cart, $product1);
        $manager->persist($cartProduct1);
        $this->addReference('cartProduct1', $cartProduct1);

        $cartProduct2 = new CartProductDE(1, $cart, $product2);
        $manager->persist($cartProduct2);
        $this->addReference('cartProduct2', $cartProduct2);

        $manager->flush();
    }

    public function getCartReference($name): CartDE
    {
        /** @var CartDE $cart */
        $cart = parent::getReference($name);

        return $cart;
    }
}
