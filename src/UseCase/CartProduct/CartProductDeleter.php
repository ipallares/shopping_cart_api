<?php

declare(strict_types=1);

namespace App\UseCase\CartProduct;

use App\Logic\Converter\CartEntityToJsonObject;
use App\Repository\CartProductRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class CartProductDeleter
{
    private CartProductRepository $cartProductRepository;
    private CartEntityToJsonObject $cartEntityToJsonObject;

    public function __construct(CartProductRepository $cartProductRepository, CartEntityToJsonObject $cartEntityToJsonObject)
    {
        $this->cartProductRepository = $cartProductRepository;
        $this->cartEntityToJsonObject = $cartEntityToJsonObject;
    }

    /**
     * @param string $id
     *
     * @return object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(string $id): object
    {
        $cartProduct = $this->cartProductRepository->findWithCertainty($id);
        $this->cartProductRepository->remove($cartProduct);

        return $this->cartEntityToJsonObject->convert($cartProduct->getCart());
    }
}
