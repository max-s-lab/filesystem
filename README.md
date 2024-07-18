<h1 align="center">
  MaxSLab Filesystem Library 
</h1>

[![Latest Version](https://img.shields.io/github/tag/max-s-lab/filesystem.svg)](https://github.com/max-s-lab/filesystem/releases)
![php 7.4+](https://img.shields.io/badge/php-min%207.4.0-blue.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/max-s-lab/filesystem/blob/master/LICENSE)

This is a library for convenient and uncomplicated work with local filesystem without unnecessary dependencies, which uses only PHP.

## Advantages

1. Easy to use.
2. Does not require dependencies.
3. Allows you to upload files without creating a directory for them in advance.
4. Allows you not to worry about symbols separating directories and adapts to the operating system.

## Installation
Run
```
$ php composer.phar require max-s-lab/filesystem
```

or add

```
"max-s-lab/filesystem": "^3.0"
```

to the ```require``` section of your `composer.json` file.

## Usage

This library can work in two modes: normal and strict. By default, the normal mode is used.

If an error has occurred and strict mode is enabled, exceptions will be thrown. 

If strict mode is disabled, then exceptional situations will be handled without throwing exceptions.

### Initializing
```
use MaxSLab\Filesystem\Filesystem;

$filesystem = new Filesystem('/var/www/some-directory');
```

### Common methods
```
$filesystem->prepareFullPath('test.txt');
$filesystem->prepareFullPath('test-directory');
$filesystem->listPathnames('*.txt');
```

### Check existing methods
```
$filesystem->fileExists('test.txt');
$filesystem->directoryExists('test-directory');
```

### Writing to file and deleting a file
```
$filesystem->writeToFile('test.txt', 'Test');
$filesystem->deleteFile('test.txt');
```

### Copying and moving a file
```
$filesystem->copyFile('test.txt', 'copy-directory/copy.txt');
$filesystem->moveFile('test.txt', 'move-directory/copy.txt');
```

### File get methods
```
$filesystem->getFileContent('test.txt');
$filesystem->getFileSize('test.txt');
$filesystem->getFileMimeType('test.txt');
$filesystem->getFileLastModifiedTime('test.txt');
```

### Creating and deleting a directory
```
$filesystem->createDirectory('test-directory');
$filesystem->deleteDirectory('test-directory');
```

### Methods for cleaning the directory
```
$filesystem->deleteAllDirectories('test-directory');
$filesystem->deleteEmptyDirectories('test-directory');
```
