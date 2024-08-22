<?php

namespace OE\factories\exceptions;

use Throwable;

class CannotSaveModelException extends \RuntimeException
{
    public function __construct(array $errors = [], array $modelAttributes = [], $code = 0, Throwable $previous = null)
    {
        $messages = [];
        foreach ($errors as $attribute => $attributeMessages) {
            $messages[] = "{$attribute}: " . implode(", ", $attributeMessages);
        }
        $errorMessage = implode('\n', $messages) . "\nModel attributes:\n";
        if (count($modelAttributes)) {
            $errorMessage .= print_r($modelAttributes, true);
        } else {
            $errorMessage .= '[not given]';
        }

        parent::__construct($errorMessage, $code, $previous);
    }
}
