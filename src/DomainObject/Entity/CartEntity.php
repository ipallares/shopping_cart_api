<?php

declare(strict_types=1);

namespace App\DomainObject\Entity;

use App\DomainObject\ValueObject\UuidVO;
use DateTimeInterface;
use InvalidArgumentException;
use Tightenco\Collect\Support\Collection;

class CartEntity
{
    private UuidVO $id;
    private DateTimeInterface $creationDate;
    private DateTimeInterface $lastModified;

    /**
     * @var Collection <int, CartProductEntity>
     */
    private Collection $cartProducts;

    public function __construct(
        string $id,
        DateTimeInterface $creationDate,
        DateTimeInterface $lastModified,
        Collection $cartProducts
    ) {
        $this->validateLastModifiedEqualsOrAfterCreationDate($creationDate, $lastModified);
        $this->id = new UuidVO($id);
        $this->creationDate = $creationDate;
        $this->lastModified = $lastModified;
        $this->cartProducts = $cartProducts;
    }

    public function getId(): string
    {
        return $this->id->getId();
    }

    public function getCreationDate(): DateTimeInterface
    {
        return $this->creationDate;
    }

    public function getLastModified(): DateTimeInterface
    {
        return $this->lastModified;
    }

    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    /**
     * @param DateTimeInterface $creationDate
     * @param DateTimeInterface $lastModified
     */
    private function validateLastModifiedEqualsOrAfterCreationDate(
        DateTimeInterface $creationDate,
        DateTimeInterface $lastModified
    ): void {
        if ($creationDate->getTimestamp() > $lastModified->getTimestamp()) {
            throw new InvalidArgumentException(
                "Last modified data can't be previous to creation date (
                    Creation Date: '" . $creationDate->format('d.m.Y H:i:s') . "',
                    Last modified Date: '" . $lastModified->format('d.m.Y H:i:s') . "')"
            );
        }
    }
}
