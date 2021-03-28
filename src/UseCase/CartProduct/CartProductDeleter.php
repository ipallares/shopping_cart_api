<?php

declare(strict_types=1);

namespace App\UseCase\CartProduct;

use App\Repository\CartProductRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class CartProductDeleter
{
    private CartProductRepository $cartProductRepository;

    public function __construct(CartProductRepository $cartProductRepository)
    {
        $this->cartProductRepository = $cartProductRepository;
    }

    /**
     * @param string $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(string $id): void
    {
        $cartProduct = $this->cartProductRepository->findWithCertainty($id);
        $this->cartProductRepository->remove($cartProduct);
    }
}
