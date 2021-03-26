<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartDE;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartDE|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartDE|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartDE[]    findAll()
 * @method CartDE[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartDE::class);
    }

    /**
     * @param CartDE $cart
     *
     * @return CartDE
     *
     * @throws ORMException
     */
    public function save(CartDE $cart): CartDE
    {
        $this->_em->persist($cart);
        $this->_em->flush();

        return $cart;
    }
}
