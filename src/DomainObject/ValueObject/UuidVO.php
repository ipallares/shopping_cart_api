<?php

declare(strict_types=1);

namespace App\DomainObject\ValueObject;

use InvalidArgumentException;

class UuidVO
{
    private string $id;

    public function __construct(string $id)
    {
        $this->validateUuid($id);
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    private function validateUuid(string $id): void
    {
        if (!uuid_is_valid($id)) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $id));
        }
    }
}
