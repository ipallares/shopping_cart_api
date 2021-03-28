<?php

declare(strict_types=1);

namespace App\Tests\core\application\services\validators;

use App\Logic\Validator\JsonSchemaValidator;
use Exception;
use JsonSchema\Exception\InvalidSchemaException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class JsonSchemaValidatorTest extends KernelTestCase
{
    private string $schemaPath;

    private JsonSchemaValidator $validator;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->validator = self::$kernel->getContainer()->get(JsonSchemaValidator::class);
        $this->schemaPath = self::$kernel->getContainer()->getParameter('cart_api_path');
    }

    public function testValidJson(): void
    {
        $this->assertValidJson($this->getInputJsonString());
    }

    public function testValidJson_missingNotRequiredCartInfo(): void
    {
        // cart id
        $jsonObject = $this->getInputJsonObject();
        unset($jsonObject->id);
        $this->assertValidJson(json_encode($jsonObject));

        // creation date
        unset($jsonObject->creationDate);
        $this->assertValidJson(json_encode($jsonObject));

        // Last modified
        unset($jsonObject->lastModified);
        $this->assertValidJson(json_encode($jsonObject));
    }

    public function testValidJson_missingNotRequiredCartProductId(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $cartProduct1 = $jsonObject->cartProducts[0];
        unset($cartProduct1->id);
        $this->assertValidJson(json_encode($jsonObject));
    }

    public function testInvalidJson_additionalCartField(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $jsonObject->additionalField = 'foo';

        $this->expectException(InvalidSchemaException::class);
        $this->validator->validate(json_encode($jsonObject), $this->schemaPath);
    }

    public function testInvalidJson_additionalCartProductField(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $cartProduct1 = $jsonObject->cartProducts[0];
        $cartProduct1->additionalField = 'foo';

        $this->expectException(InvalidSchemaException::class);
        $this->validator->validate(json_encode($jsonObject), $this->schemaPath);
    }

    public function testInvalidJson_missingRequiredProductQuantity(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $cartProduct1 = $jsonObject->cartProducts[0];
        unset($cartProduct1->quantity);

        $this->expectException(InvalidSchemaException::class);
        $this->validator->validate(json_encode($jsonObject), $this->schemaPath);
    }

    public function testInvalidJson_missingRequiredProductName(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $cartProduct1 = $jsonObject->cartProducts[0];
        unset($cartProduct1->productName);

        $this->expectException(InvalidSchemaException::class);
        $this->validator->validate(json_encode($jsonObject), $this->schemaPath);
    }

    public function testInvalidJson_missingRequiredProductPrice(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $cartProduct1 = $jsonObject->cartProducts[0];
        unset($cartProduct1->productPrice);

        $this->expectException(InvalidSchemaException::class);
        $this->validator->validate(json_encode($jsonObject), $this->schemaPath);
    }

    public function testInvalidJson_missingRequiredProductStock(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $cartProduct1 = $jsonObject->cartProducts[0];
        unset($cartProduct1->productStock);

        $this->expectException(InvalidSchemaException::class);
        $this->validator->validate(json_encode($jsonObject), $this->schemaPath);
    }

    public function testInvalidJson_missingRequiredCartProductPrice(): void
    {
        $jsonObject = $this->getInputJsonObject();
        $cartProduct1 = $jsonObject->cartProducts[0];
        unset($cartProduct1->productStock);

        $this->expectException(InvalidSchemaException::class);
        $this->validator->validate(json_encode($jsonObject), $this->schemaPath);
    }

    private function assertValidJson(string $json): void
    {
        try{
            $exceptionThrown = false;
            $this->validator->validate($json, $this->schemaPath);
        } catch (Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown);
    }

    private function getInputJsonString(): string
    {
        return file_get_contents('tests/Logic/Validator/json-examples/valid-cart-api-example.json');
    }

    private function getInputJsonObject(): object
    {
        return json_decode($this->getInputJsonString());
    }
}
