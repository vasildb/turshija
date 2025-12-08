<?php

namespace Vasil\Turshija\Exceptions;

use Throwable;

class TemplateFileDoesNotExist extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null)
    {
        $message = 'Template file [' . $message . '] does not exist.';
        return parent::__construct($message, $code, $previous);
    }
}
