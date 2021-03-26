<?php

declare(strict_types=1);

namespace App\Tests\DomainObject\ValueObject;

use App\DomainObject\ValueObject\UuidVO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UuidVoTest extends KernelTestCase
{
    public function testValidUuid(): void
    {
        $id = uuid_create();
        $uuid = new UuidVO($id);
        $this->assertEquals($id, $uuid->getId());
    }

    public function testInvalidUuid(): void
    {
        $id = 'invalid-uuid';
        $this->expectException(\InvalidArgumentException::class);
        $uuid = new UuidVO($id);
    }
}
