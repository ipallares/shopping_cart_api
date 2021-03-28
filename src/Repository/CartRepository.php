<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * @param Cart $cart
     *
     * @return Cart
     *
     * @throws ORMException
     */
    public function save(Cart $cart): Cart
    {
        $this->_em->persist($cart);
        $this->_em->flush();

        return $cart;
    }

    public function findWithCertainty(string $id): Cart
    {
        $cart = $this->find($id);
        if (null === $cart) {
            throw new ResourceNotFoundException("No Cart with id: $id");
        }

        return $cart;
    }
}
