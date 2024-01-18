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

    /**
     * @throws FilesystemException
     */
    public function wrap(callable $function, $errorReturnValue)
    {
        try {
            return $function();
        } catch (Throwable $e) {
            return $this->processError($e->getMessage(), $errorReturnValue);
        }
    }

    /**
     * @throws FilesystemException
     */
    public function processError(string $message, $value)
    {
        if ($this->strictMode) {
            throw new FilesystemException($message);
        }

        return $value;
    }
}
