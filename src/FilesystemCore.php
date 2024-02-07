<?php

namespace MaxSLab\Filesystem;

/**
 * This class is the core of the filesystem.
 * It contains the basic methods of working with the filesystem.
 * All paths passed to methods are absolute.
 */
class FilesystemCore
{
    /** @var FilesystemOperationManager */
    private $operationManager;

    public function __construct(bool $strictMode)
    {
        $this->operationManager = new FilesystemOperationManager($strictMode);
    }

    /**
     * @throws FilesystemException
     */
    public function uploadFile(string $path, string $content): bool
    {
        $fileName = basename($path);
        $fileDirectory = str_replace($fileName, '', $path);

        if (!is_dir($fileDirectory)) {
            if (!$this->createDirectory($fileDirectory)) {
                return false;
            }
        }

        return $this->operationManager->wrap(function () use ($path, $content) {
            return file_put_contents($path, $content) !== false;
        });
    }

    /**
     * @throws FilesystemException
     */
    public function listPathnames(string $pattern): ?array
    {
        return $this->operationManager->wrap(function () use ($pattern) {
            $elements = glob($pattern);
            return $elements !== false ? $elements : null;
        });
    }

    /**
     * @throws FilesystemException
     */
    public function getFileContent(string $path): ?string
    {
        return $this->operationManager->wrap(function () use ($path) {
            $fileContent = file_get_contents($path);
            return $fileContent !== false ? $fileContent : null;
        });
    }

    /**
     * @throws FilesystemException
     */
    public function getFileSize(string $path): ?int
    {
        return $this->operationManager->wrap(function () use ($path) {
            $fileSize = filesize($path);
            return $fileSize !== false ? $fileSize : null;
        });
    }

    /**
     * @throws FilesystemException
     */
    public function getFileMimeType(string $path): ?string
    {
        return $this->operationManager->wrap(function () use ($path) {
            $mimeType = mime_content_type($path);
            return $mimeType !== false ? $mimeType : null;
        });
    }

    /**
     * @throws FilesystemException
     */
    public function getFileLastModifiedTime(string $path): ?int
    {
        return $this->operationManager->wrap(function () use ($path) {
            $filemtime = filemtime($path);
            return $filemtime !== false ? $filemtime : null;
        });
    }

    /**
     * @throws FilesystemException
     */
    public function deleteFile(string $path): bool
    {
        return $this->operationManager->wrap(function () use ($path) {
            return unlink($path);
        });
    }

    /**
     * @throws FilesystemException
     */
    public function copyFile(string $oldPath, string $newPath): bool
    {
        return $this->operationManager->wrap(function () use ($oldPath, $newPath) {
            return copy($oldPath, $newPath);
        });
    }

    /**
     * @throws FilesystemException
     */
    public function moveFile(string $oldPath, string $newPath): bool
    {
        return $this->operationManager->wrap(function () use ($oldPath, $newPath) {
            return rename($oldPath, $newPath);
        });
    }

    /**
     * @throws FilesystemException
     */
    public function createDirectory(string $path, int $permissions = 0777): bool
    {
        return $this->operationManager->wrap(function () use ($path, $permissions) {
            return mkdir($path, $permissions, true);
        });
    }

    /**
     * @throws FilesystemException
     */
    public function deleteDirectory(string $path): bool
    {
        $pathnames = $this->findAllPathnames($path);

        if ($pathnames === null) {
            return false;
        }

        foreach ($pathnames as $pathname) {
            if (is_dir($pathname)) {
                if (!$this->deleteDirectory($pathname)) {
                    return false;
                }
            } elseif (!$this->deleteFile($pathname)) {
                return false;
            }
        }

        return $this->operationManager->wrap(function () use ($path) {
            return rmdir($path);
        });
    }

    /**
     * @throws FilesystemException
     */
    public function deleteAllDirectories(string $path): bool
    {
        if (!is_dir($path)) {
            return $this->operationManager->processError('The specified path is not a directory.', false);
        }

        $pathnames = $this->findAllPathnames($path);

        if ($pathnames === null) {
            return false;
        }

        foreach ($pathnames as $pathname) {
            if (!is_dir($pathname)) {
                continue;
            }

            if (!$this->deleteDirectory($pathname)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws FilesystemException
     */
    public function deleteEmptyDirectories(string $path): bool
    {
        if (!is_dir($path)) {
            return $this->operationManager->processError('The specified path is not a directory.', false);
        }

        $pathnames = $this->findAllPathnames($path);

        if ($pathnames === null) {
            return false;
        }

        foreach ($pathnames as $pathname) {
            if (!is_dir($pathname) || !empty($this->findAllPathnames($pathname))) {
                continue;
            }

            if (!$this->deleteDirectory($pathname)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string[]|null
     */
    private function findAllPathnames(string $path): ?array
    {
        if (substr($path, -1) === DIRECTORY_SEPARATOR) {
            $path = $path . "*";
        } else {
            $path = $path . DIRECTORY_SEPARATOR . "*";
        }

        $pathnames = glob($path);

        if ($pathnames === false) {
            return $this->operationManager->processError('An attempt to search files/directories failed.', null);
        }

        return $pathnames;
    }
}
