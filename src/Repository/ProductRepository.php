<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProductDE;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductDE|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductDE|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductDE[]    findAll()
 * @method ProductDE[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductDE::class);
    }
}
