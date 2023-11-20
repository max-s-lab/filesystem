<?php

namespace MaxSLab\Filesystem;

use Throwable;

class FilesystemOperationManager
{
    /** @var bool */
    private $strictMode;

    public function __construct(bool $strictMode)
    {
        $this->strictMode = $strictMode;
    }

    public function wrap(callable $function, $errorReturnValue)
    {
        try {
            return $function();
        } catch (Throwable $e) {
            if ($this->strictMode) {
                throw new FilesystemException($e->getMessage());
            } else {
                return $errorReturnValue;
            }
        }
    }
}
