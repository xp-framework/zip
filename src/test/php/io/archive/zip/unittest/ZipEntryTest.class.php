<?php namespace io\archive\zip\unittest;

use io\archive\zip\{ZipDirEntry, ZipFileEntry};
use test\{Assert, Test};

/**
 * TestCase
 *
 * @see   io.archive.zip.ZipFileEntry
 * @see   io.archive.zip.ZipDirEntry
 */
class ZipEntryTest extends AbstractZipFileTest {

  #[Test]
  public function simpleFileName() {
    Assert::equals('Hello.txt', (new ZipFileEntry('Hello.txt'))->getName());
  }

  #[Test]
  public function simpleDirName() {
    Assert::equals('Hello/', (new ZipDirEntry('Hello'))->getName());
  }

  #[Test]
  public function backslashesReplacedInFile() {
    Assert::equals('hello/World.txt', (new ZipFileEntry('hello\\World.txt'))->getName());
  }

  #[Test]
  public function backslashesReplacedInDir() {
    Assert::equals('hello/World/', (new ZipDirEntry('hello\\World'))->getName());
  }

  #[Test]
  public function trailingSlashesInDirNormalized() {
    Assert::equals('hello/', (new ZipDirEntry('hello//'))->getName());
  }

  #[Test]
  public function trailingBackslashesInDirNormalized() {
    Assert::equals('hello/', (new ZipDirEntry('hello\\\\'))->getName());
  }

  #[Test]
  public function composeFileFromString() {
    Assert::equals('META-INF/manifest.ini', (new ZipFileEntry('META-INF', 'manifest.ini'))->getName());
  }

  #[Test]
  public function composeDirFromString() {
    Assert::equals('META-INF/services/', (new ZipDirEntry('META-INF', 'services'))->getName());
  }

  #[Test]
  public function composeFileFromDirAndString() {
    Assert::equals('META-INF/manifest.ini', (new ZipFileEntry(new ZipDirEntry('META-INF'), 'manifest.ini'))->getName());
  }

  #[Test]
  public function composeDirFromDirAndString() {
    Assert::equals('META-INF/services/', (new ZipDirEntry(new ZipDirEntry('META-INF'), 'services'))->getName());
  }
}