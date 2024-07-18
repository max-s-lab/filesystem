<?php

namespace MaxSLab\Filesystem;

/**
 * The Filesystem class is a class that allows to work with local filesystem.
 * This class allows you to upload files without creating a directory for them in advance and
 * you not to worry about symbols separating directories and adapts to the operating system.
 * Important note! All paths passed to the function are relative.
 *
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 * @since 2023-11-20
 * @category Filesystem
 * @package MaxSLab\Filesystem
 */
class Filesystem
{
    protected string $location;

    protected FilesystemCore $core;

    /**
     * @param string $location The absolute path to the directory where the actions will be performed.
     * @param bool $strictMode This attribute determines the severity of error handling.
     * If an error has occurred and the mode is enabled, an exception will be thrown.
     * If turned off, the error will be caught automatically.
     */
    public function __construct(string $location, bool $strictMode = false)
    {
        $this->core = new FilesystemCore($strictMode);
        $this->location = $location;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function prepareFullPath(string $path): string
    {
        $path = $this->location . DIRECTORY_SEPARATOR . $path;

        return str_replace(['/', '\\', '//'], DIRECTORY_SEPARATOR, trim($path));
    }

    public function fileExists(string $path): bool
    {
        return is_file($this->prepareFullPath($path));
    }

    public function directoryExists(string $path): bool
    {
        return is_dir($this->prepareFullPath($path));
    }

    /**
     * Uploading a file with creating a directory for it.
     *
     * @throws FilesystemException
     */
    public function uploadFile(string $path, string $content, int $flags = 0): bool
    {
        return $this->core->uploadFile($this->prepareFullPath($path), $content, $flags);
    }

    /**
     * @throws FilesystemException
     */
    public function listPathnames(string $pattern): ?array
    {
        return $this->core->listPathnames($this->prepareFullPath($pattern));
    }

    /**
     * @throws FilesystemException
     */
    public function getFileContent(string $path): ?string
    {
        return $this->core->getFileContent($this->prepareFullPath($path));
    }

    /**
     * @throws FilesystemException
     */
    public function getFileSize(string $path): ?int
    {
        return $this->core->getFileSize($this->prepareFullPath($path));
    }

    /**
     * @throws FilesystemException
     */
    public function getFileMimeType(string $path): ?string
    {
        return $this->core->getFileMimeType($this->prepareFullPath($path));
    }

    /**
     * @throws FilesystemException
     */
    public function getFileLastModifiedTime(string $path): ?int
    {
        return $this->core->getFileLastModifiedTime($this->prepareFullPath($path));
    }

    /**
     * @throws FilesystemException
     */
    public function deleteFile(string $path): bool
    {
        return $this->core->deleteFile($this->prepareFullPath($path));
    }

    /**
     * Copying a file with creating a directory for it.
     *
     * @throws FilesystemException
     */
    public function copyFile(string $oldPath, string $newPath): bool
    {
        return $this->core->copyFile($this->prepareFullPath($oldPath), $this->prepareFullPath($newPath));
    }

    /**
     * Moving a file with creating a directory for it.
     *
     * @throws FilesystemException
     */
    public function moveFile(string $oldPath, string $newPath): bool
    {
        return $this->core->moveFile($this->prepareFullPath($oldPath), $this->prepareFullPath($newPath));
    }

    /**
     * Recursive creating a directory.
     *
     * @throws FilesystemException
     */
    public function createDirectory(string $path, int $permissions = 0777): bool
    {
        return $this->core->createDirectory($this->prepareFullPath($path), $permissions);
    }

    /**
     * Recursive deleting a directory.
     *
     * @throws FilesystemException
     */
    public function deleteDirectory(string $path): bool
    {
        return $this->core->deleteDirectory($this->prepareFullPath($path));
    }

    /**
     * Deleting all directories together with the files attached to them from a specific directory,
     * leaving only the files in the root of the directory.
     *
     * @throws FilesystemException
     */
    public function deleteAllDirectories(string $path): bool
    {
        return $this->core->deleteAllDirectories($this->prepareFullPath($path));
    }

    /**
     * Deleting empty directories from a specific directory,
     * leaving only the files and non empty directories in the root of the directory.
     *
     * @throws FilesystemException
     */
    public function deleteEmptyDirectories(string $path): bool
    {
        return $this->core->deleteEmptyDirectories($this->prepareFullPath($path));
    }
}
