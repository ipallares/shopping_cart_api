<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartProductDE;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartProductDE|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartProductDE|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartProductDE[]    findAll()
 * @method CartProductDE[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartProductDE::class);
    }
}
