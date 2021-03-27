<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartProduct[]    findAll()
 * @method CartProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartProduct::class);
    }

    /**
     * @param CartProduct $cartProduct
     *
     * @return CartProduct
     *
     * @throws ORMException
     */
    public function save(CartProduct $cartProduct): CartProduct
    {
        $this->_em->persist($cartProduct);
        $this->_em->flush();

        return $cartProduct;
    }
}
