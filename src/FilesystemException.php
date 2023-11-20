<?php

namespace MaxSLab\Filesystem;

use Exception;
use Throwable;

class FilesystemException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Filesystem error: $message", $code, $previous);
    }
}
