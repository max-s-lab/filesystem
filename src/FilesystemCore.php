<?php

namespace MaxSLab\Filesystem;

/**
 * This class is the core of the filesystem.
 * It contains the basic methods of working with the filesystem.
 * All paths passed to methods are absolute.
 */
class FilesystemCore
{
    protected FilesystemOperationManager $operationManager;

    public function __construct(bool $strictMode)
    {
        $this->operationManager = new FilesystemOperationManager($strictMode);
    }

    /**
     * @throws FilesystemException
     */
    public function uploadFile(string $path, string $content, int $flags = 0): bool
    {
        if (!$this->createDirectoryByFilePath($path)) {
            return false;
        }

        return $this->operationManager->wrap(function () use ($path, $content, $flags) {
            return file_put_contents($path, $content, $flags) !== false;
        });
    }

    /**
     * @throws FilesystemException
     */
    public function listPathnames(string $pattern): ?array
    {
        return $this->operationManager->wrap(function () use ($pattern) {
            return $this->returnSuccessResultOrNull(glob($pattern));
        });
    }

    /**
     * @throws FilesystemException
     */
    public function getFileContent(string $path): ?string
    {
        return $this->operationManager->wrap(function () use ($path) {
            return $this->returnSuccessResultOrNull(file_get_contents($path));
        });
    }

    /**
     * @throws FilesystemException
     */
    public function getFileSize(string $path): ?int
    {
        return $this->operationManager->wrap(function () use ($path) {
            return $this->returnSuccessResultOrNull(filesize($path));
        });
    }

    /**
     * @throws FilesystemException
     */
    public function getFileMimeType(string $path): ?string
    {
        return $this->operationManager->wrap(function () use ($path) {
            return $this->returnSuccessResultOrNull(mime_content_type($path));
        });
    }

    /**
     * @throws FilesystemException
     */
    public function getFileLastModifiedTime(string $path): ?int
    {
        return $this->operationManager->wrap(function () use ($path) {
            return $this->returnSuccessResultOrNull(filemtime($path));
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
        if (!$this->createDirectoryByFilePath($newPath)) {
            return false;
        }

        return $this->operationManager->wrap(function () use ($oldPath, $newPath) {
            return copy($oldPath, $newPath);
        });
    }

    /**
     * @throws FilesystemException
     */
    public function moveFile(string $oldPath, string $newPath): bool
    {
        if (!$this->createDirectoryByFilePath($newPath)) {
            return false;
        }

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

        return $this->deleteEmptyDirectory($path);
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
    public function deleteEmptyDirectories(string $path, bool $recursive): bool
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

            if (!empty($this->findAllPathnames($pathname))) {
                if (!$recursive) {
                    continue;
                }

                if (!$this->deleteEmptyDirectories($pathname, true)) {
                    return false;
                }
            }

            if (!$this->deleteEmptyDirectory($pathname)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string[]|null
     *
     * @throws FilesystemException
     */
    protected function findAllPathnames(string $path): ?array
    {
        if (substr($path, -1) === DIRECTORY_SEPARATOR) {
            $path = $path . '*';
        } else {
            $path = $path . DIRECTORY_SEPARATOR . '*';
        }

        $pathnames = glob($path);

        if ($pathnames === false) {
            return $this->operationManager->processError('An attempt to search files/directories failed.', null);
        }

        return $pathnames;
    }

    /**
     * @throws FilesystemException
     */
    protected function createDirectoryByFilePath(string $filePath): bool
    {
        $dirname = dirname($filePath);

        return is_dir($dirname) || $this->createDirectory($dirname);
    }

    /**
     * @throws FilesystemException
     */
    protected function deleteEmptyDirectory(string $path): bool
    {
        return $this->operationManager->wrap(function () use ($path) {
            return rmdir($path);
        });
    }

    protected function returnSuccessResultOrNull($result)
    {
        return $result !== false ? $result : null;
    }
}
