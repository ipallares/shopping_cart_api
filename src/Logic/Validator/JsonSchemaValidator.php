<?php

declare(strict_types=1);

namespace App\Logic\Validator;

use JsonSchema\Exception\InvalidSchemaException;
use JsonSchema\Validator;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class JsonSchemaValidator
{
    private Validator $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string $json
     * @param string $schemaPath
     *
     * @throws InvalidSchemaException
     */
    public function validate(string $json, string $schemaPath): void
    {
        if (!$jsonSchema = file_get_contents($schemaPath)) {
            throw new FileNotFoundException('File not found in path: '.$schemaPath);
        }

        $schemaObject = json_decode($jsonSchema, false, 512, JSON_THROW_ON_ERROR);
        $jsonObject = json_decode($json, false, 512, JSON_THROW_ON_ERROR);

        $this->validator->validate($jsonObject, $schemaObject);

        if (!$this->validator->isValid()) {
            $errorMessages = $this->getErrorMessages($this->validator->getErrors());
            throw new InvalidSchemaException($errorMessages);
        }
    }

    private function getErrorMessages(array $errors): string
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMsg = 'Json Schema Error. Field "'.$error['property'].'". ';
            $errorMsg .= $error['message'];
            $errorMessages[] = $errorMsg;
        }

        return implode("\n", $errorMessages);
    }
}
