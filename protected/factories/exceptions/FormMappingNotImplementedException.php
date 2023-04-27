<?php

namespace OE\factories\exceptions;

class FormMappingNotImplementedException extends \BadMethodCallException
{
    public function __construct(string $factory_class, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($factory_class . ' must implement form mapping behaviour', $code, $previous);
    }
}