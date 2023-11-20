<?php

namespace MaxSLab\Filesystem\Tests;

use PHPUnit\Framework\TestCase;
use MaxSLab\Filesystem\Filesystem;

/**
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 */
class FilesystemTest extends TestCase
{
    const FILE_NAME = 'test.txt';
    const FILE_CONTENT = 'Test file';

    const TEST_DIRECTORY = 'test-dir';
    const NON_EMPTY_DIRECTORY = 'non-empty';
    const COPYING_DIRECTORY = 'test-copying';
    const MOVING_DIRECTORY = 'test-moving';

    /** @var Filesystem */
    private $filesystem;

    public function setUp(): void
    {
        $this->filesystem = new Filesystem(__DIR__ . '/../test-tmp');
    }

    public function testDeletingDirectory()
    {
        $this->filesystem->createDirectory(self::TEST_DIRECTORY);
        $this->assertTrue($this->filesystem->directoryExists(self::TEST_DIRECTORY));

        $this->filesystem->deleteDirectory(self::TEST_DIRECTORY);
        $this->assertFalse($this->filesystem->directoryExists(self::TEST_DIRECTORY));
    }

    public function testUploadingFile()
    {
        $this->filesystem->uploadFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertTrue($this->filesystem->fileExists(self::FILE_NAME));
    }

    public function testGettingFileContent()
    {
        $this->filesystem->uploadFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertEquals(self::FILE_CONTENT, $this->filesystem->getFileContent(self::FILE_NAME));
    }

    public function testDeletingFile()
    {
        $this->filesystem->uploadFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertTrue($this->filesystem->fileExists(self::FILE_NAME));

        $this->filesystem->deleteFile(self::FILE_NAME);
        $this->assertFalse($this->filesystem->fileExists(self::FILE_NAME));
    }

    public function testCopyingFile()
    {
        $this->filesystem->uploadFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertTrue($this->filesystem->fileExists(self::FILE_NAME));

        $this->filesystem->createDirectory(self::COPYING_DIRECTORY);
        $this->filesystem->copyFile(self::FILE_NAME, self::COPYING_DIRECTORY . '/' . self::FILE_NAME);
        $this->assertTrue($this->filesystem->fileExists(self::COPYING_DIRECTORY . '/' . self::FILE_NAME));
    }

    public function testMovingFile()
    {
        $this->filesystem->uploadFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertTrue($this->filesystem->fileExists(self::FILE_NAME));

        $this->filesystem->createDirectory(self::MOVING_DIRECTORY);
        $this->filesystem->moveFile(self::FILE_NAME, self::MOVING_DIRECTORY . '/' . self::FILE_NAME);

        $this->assertFalse($this->filesystem->fileExists(self::FILE_NAME));
        $this->assertTrue($this->filesystem->fileExists(self::MOVING_DIRECTORY . '/' . self::FILE_NAME));
    }

    public function testDeletingEmptyDirectory()
    {
        $nonEmptyDirectory = self::TEST_DIRECTORY . '/' . self::NON_EMPTY_DIRECTORY;

        $this->filesystem->uploadFile($nonEmptyDirectory . '/' . self::FILE_NAME, self::FILE_CONTENT);

        for ($i = 0; $i < 3; $i++) {
            $this->filesystem->createDirectory(self::TEST_DIRECTORY . "/$i");
        }

        $this->filesystem->deleteEmptyDirectories(self::TEST_DIRECTORY);

        for ($i = 0; $i < 3; $i++) {
            $this->assertFalse($this->filesystem->directoryExists(self::TEST_DIRECTORY . "/$i"));
        }

        $this->assertTrue($this->filesystem->directoryExists($nonEmptyDirectory));

        $this->filesystem->deleteDirectory(self::TEST_DIRECTORY);
    }

    public function testDeletingAllDirectory()
    {
        $nonEmptyDirectory = self::TEST_DIRECTORY . '/' . self::NON_EMPTY_DIRECTORY;

        $this->filesystem->uploadFile($nonEmptyDirectory . '/' . self::FILE_NAME, self::FILE_CONTENT);

        for ($i = 0; $i < 3; $i++) {
            $this->filesystem->createDirectory(self::TEST_DIRECTORY . "/$i");
        }

        $this->filesystem->deleteAllDirectories(self::TEST_DIRECTORY);

        for ($i = 0; $i < 3; $i++) {
            $this->assertFalse($this->filesystem->directoryExists(self::TEST_DIRECTORY . "/$i"));
        }

        $this->assertFalse($this->filesystem->directoryExists($nonEmptyDirectory));
    }

    protected function tearDown(): void
    {
        $this->filesystem = null;
    }
}
