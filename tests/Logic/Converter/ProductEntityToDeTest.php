<?php

declare(strict_types=1);

namespace App\Tests\Logic\Converter;

use App\DataFixtures\AppFixtures;
use App\DomainObject\Entity\ProductEntity;
use App\Logic\Converter\ProductEntityToDE;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductEntityToDeTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private ProductEntityToDE $productEntityToDE;
    private EntityManagerInterface $manager;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->productEntityToDE = self::$container->get(ProductEntityToDE::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testConvert(): void
    {
        $id = uuid_create();
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 3;

        $productEntity = new ProductEntity(
            $id,
            $productName,
            $productPrice,
            $productStock
        );

        $productDE = $this->productEntityToDE->convert($productEntity);

        $this->assertEquals($id, $productDE->getId());
        $this->assertEquals($productName, $productDE->getName());
        $this->assertEquals($productPrice, $productDE->getPrice());
        $this->assertEquals($productStock, $productDE->getStock());
    }
}
