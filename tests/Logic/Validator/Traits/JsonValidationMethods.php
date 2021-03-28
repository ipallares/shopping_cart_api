<?php

declare(strict_types=1);

namespace App\Tests\Logic\Validator\Traits;

trait JsonValidationMethods
{
    private function getInputJsonString(): string
    {
        return file_get_contents('tests/Logic/Validator/json-examples/valid-cart-api-example-v1.json');
    }

    private function getInputJsonObject(): object
    {
        return json_decode($this->getInputJsonString());
    }
}
