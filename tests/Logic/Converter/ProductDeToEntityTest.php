<?php

declare(strict_types=1);

namespace App\Tests\Logic\Converter;

use App\DataFixtures\AppFixtures;
use App\Logic\Converter\ProductDeToEntity;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductDeToEntityTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private ProductDeToEntity $productDeToEntity;
    private EntityManagerInterface $manager;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->productDeToEntity = self::$container->get(ProductDeToEntity::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testConvert(): void
    {
        $productDE = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $productEntity = $this->productDeToEntity->convert($productDE);
        $this->assertEquals($productDE->getId(), $productEntity->getId());
        $this->assertEquals($productDE->getName(), $productEntity->getName());
        $this->assertEquals($productDE->getPrice(), $productEntity->getPrice());
        $this->assertEquals($productDE->getStock(), $productEntity->getStock());
    }
}
