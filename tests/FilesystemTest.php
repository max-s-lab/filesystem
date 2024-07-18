<?php

namespace MaxSLab\Filesystem\Tests;

use MaxSLab\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

/**
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 */
class FilesystemTest extends TestCase
{
    private const FILE_NAME = 'test.txt';
    private const FILE_CONTENT = 'Test file';
    private const FILE_SIZE = 9;
    private const FILE_MIME_TYPE = 'text/plain';

    private const TEST_DIRECTORY = 'test-dir';
    private const NON_EMPTY_DIRECTORY = 'non-empty';
    private const COPYING_DIRECTORY = 'test-copying';
    private const MOVING_DIRECTORY = 'test-moving';

    private const NOT_EXISTING_DIRECTORY_NAME = 'not-existing-dir';
    private const NOT_EXISTING_FILE_NAME = 'not-existing-dir.txt';

    private Filesystem $filesystem;

    public function setUp(): void
    {
        $this->filesystem = new Filesystem(dirname(__DIR__) . '/test-tmp');
    }

    public function testCreatingAndDeletingDirectory()
    {
        $this->filesystem->createDirectory(self::TEST_DIRECTORY);
        $this->assertTrue($this->filesystem->directoryExists(self::TEST_DIRECTORY));

        $this->filesystem->deleteDirectory(self::TEST_DIRECTORY);
        $this->assertFalse($this->filesystem->directoryExists(self::TEST_DIRECTORY));

        $this->assertFalse($this->filesystem->deleteDirectory(self::NOT_EXISTING_DIRECTORY_NAME));
    }

    public function testWritingToFileAndDeletingFile()
    {
        $this->filesystem->writeToFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertTrue($this->filesystem->fileExists(self::FILE_NAME));

        $this->filesystem->deleteFile(self::FILE_NAME);
        $this->assertFalse($this->filesystem->fileExists(self::FILE_NAME));

        $this->assertFalse($this->filesystem->deleteFile(self::NOT_EXISTING_FILE_NAME));
    }

    public function testGettingFileContent()
    {
        $this->filesystem->writeToFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertEquals(self::FILE_CONTENT, $this->filesystem->getFileContent(self::FILE_NAME));

        $this->assertNull($this->filesystem->getFileContent(self::NOT_EXISTING_FILE_NAME));
    }

    public function testGettingFileSize()
    {
        $this->filesystem->writeToFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertEquals(self::FILE_SIZE, $this->filesystem->getFileSize(self::FILE_NAME));

        $this->assertNull($this->filesystem->getFileSize(self::NOT_EXISTING_FILE_NAME));
    }

    public function testGettingFileMimeType()
    {
        $this->filesystem->writeToFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertEquals(self::FILE_MIME_TYPE, $this->filesystem->getFileMimeType(self::FILE_NAME));

        $this->assertNull($this->filesystem->getFileMimeType(self::NOT_EXISTING_FILE_NAME));
    }

    public function testGettingFileLastModifiedTime()
    {
        $this->filesystem->writeToFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertEquals(time(), $this->filesystem->getFileLastModifiedTime(self::FILE_NAME));

        $this->assertNull($this->filesystem->getFileLastModifiedTime(self::NOT_EXISTING_FILE_NAME));
    }

    public function testListPathnames()
    {
        $this->filesystem->writeToFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertEquals(
            [$this->filesystem->prepareFullPath(self::FILE_NAME)],
            $this->filesystem->listPathnames('*')
        );

        $this->assertEquals([], $this->filesystem->listPathnames(self::NOT_EXISTING_DIRECTORY_NAME));
    }

    public function testCopyingFile()
    {
        $this->filesystem->writeToFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertTrue($this->filesystem->fileExists(self::FILE_NAME));

        $this->filesystem->copyFile(self::FILE_NAME, self::COPYING_DIRECTORY . '/' . self::FILE_NAME);
        $this->assertTrue($this->filesystem->fileExists(self::COPYING_DIRECTORY . '/' . self::FILE_NAME));

        $this->assertFalse($this->filesystem->copyFile(
            self::NOT_EXISTING_FILE_NAME,
            self::COPYING_DIRECTORY . '/' . self::FILE_NAME
        ));
    }

    public function testMovingFile()
    {
        $this->filesystem->writeToFile(self::FILE_NAME, self::FILE_CONTENT);
        $this->assertTrue($this->filesystem->fileExists(self::FILE_NAME));

        $this->filesystem->moveFile(self::FILE_NAME, self::MOVING_DIRECTORY . '/' . self::FILE_NAME);
        $this->assertFalse($this->filesystem->fileExists(self::FILE_NAME));
        $this->assertTrue($this->filesystem->fileExists(self::MOVING_DIRECTORY . '/' . self::FILE_NAME));

        $this->assertFalse($this->filesystem->moveFile(
            self::NOT_EXISTING_FILE_NAME,
            self::MOVING_DIRECTORY . '/' . self::FILE_NAME
        ));
    }

    public function testDeletingAllDirectories()
    {
        $nonEmptyDirectory = self::TEST_DIRECTORY . '/' . self::NON_EMPTY_DIRECTORY;

        $this->filesystem->writeToFile($nonEmptyDirectory . '/' . self::FILE_NAME, self::FILE_CONTENT);

        for ($i = 0; $i < 3; $i++) {
            $this->filesystem->createDirectory(self::TEST_DIRECTORY . "/$i");
        }

        $this->filesystem->deleteAllDirectories(self::TEST_DIRECTORY);

        $this->assertTrue($this->filesystem->directoryExists(self::TEST_DIRECTORY));
        $this->assertEquals([], $this->filesystem->listPathnames(self::TEST_DIRECTORY . '/*'));

        $this->assertFalse($this->filesystem->deleteAllDirectories(self::NOT_EXISTING_DIRECTORY_NAME));

        $this->filesystem->deleteDirectory(self::TEST_DIRECTORY);
    }

    public function testRecursiveDeletingEmptyDirectories()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->filesystem->createDirectory(self::TEST_DIRECTORY . "/$i/$i");
        }

        $this->filesystem->deleteEmptyDirectories(self::TEST_DIRECTORY);

        $this->assertTrue($this->filesystem->directoryExists(self::TEST_DIRECTORY));
        $this->assertEquals([], $this->filesystem->listPathnames(self::TEST_DIRECTORY . '/*'));

        $this->assertFalse($this->filesystem->deleteEmptyDirectories(self::NOT_EXISTING_DIRECTORY_NAME));

        $this->filesystem->deleteDirectory(self::TEST_DIRECTORY);
    }

    public function testNonRecursiveDeletingEmptyDirectories()
    {
        $nonEmptyDirectory = self::TEST_DIRECTORY . '/' . self::NON_EMPTY_DIRECTORY;

        $this->filesystem->writeToFile($nonEmptyDirectory . '/' . self::FILE_NAME, self::FILE_CONTENT);

        for ($i = 0; $i < 3; $i++) {
            $this->filesystem->createDirectory(self::TEST_DIRECTORY . "/$i");
        }

        $this->filesystem->deleteEmptyDirectories(self::TEST_DIRECTORY, false);

        $this->assertEquals(
            [$this->filesystem->prepareFullPath($nonEmptyDirectory)],
            $this->filesystem->listPathnames(self::TEST_DIRECTORY . '/*')
        );

        $this->assertFalse($this->filesystem->deleteEmptyDirectories(
            self::NOT_EXISTING_DIRECTORY_NAME,
            false
        ));

        $this->filesystem->deleteDirectory(self::TEST_DIRECTORY);
    }

    protected function tearDown(): void
    {
        $this->filesystem->deleteDirectory('');
    }
}
