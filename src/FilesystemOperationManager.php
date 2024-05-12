<?php

namespace MaxSLab\Filesystem;

class FilesystemOperationManager
{
    protected bool $strictMode;

    public function __construct(bool $strictMode)
    {
        $this->strictMode = $strictMode;
    }

    /**
     * @throws FilesystemException
     */
    public function wrap(callable $function)
    {
        $result = @$function();
        $error = error_get_last();

        if ($this->strictMode && $error !== null) {
            throw new FilesystemException($error['message']);
        }

        return $result;
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
