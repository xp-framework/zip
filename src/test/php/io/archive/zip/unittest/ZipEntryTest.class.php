<?php namespace io\archive\zip\unittest;

use io\archive\zip\{ZipDirEntry, ZipFileEntry};
use unittest\Test;

/**
 * TestCase
 *
 * @see     xp://io.archive.zip.ZipFileEntry
 * @see     xp://io.archive.zip.ZipDirEntry
 */
class ZipEntryTest extends AbstractZipFileTest {

  #[Test]
  public function simpleFileName() {
    $this->assertEquals('Hello.txt', (new ZipFileEntry('Hello.txt'))->getName());
  }

  #[Test]
  public function simpleDirName() {
    $this->assertEquals('Hello/', (new ZipDirEntry('Hello'))->getName());
  }

  #[Test]
  public function backslashesReplacedInFile() {
    $this->assertEquals('hello/World.txt', (new ZipFileEntry('hello\\World.txt'))->getName());
  }

  #[Test]
  public function backslashesReplacedInDir() {
    $this->assertEquals('hello/World/', (new ZipDirEntry('hello\\World'))->getName());
  }

  #[Test]
  public function trailingSlashesInDirNormalized() {
    $this->assertEquals('hello/', (new ZipDirEntry('hello//'))->getName());
  }

  #[Test]
  public function trailingBackslashesInDirNormalized() {
    $this->assertEquals('hello/', (new ZipDirEntry('hello\\\\'))->getName());
  }

  #[Test]
  public function composeFileFromString() {
    $this->assertEquals('META-INF/manifest.ini', (new ZipFileEntry('META-INF', 'manifest.ini'))->getName());
  }

  #[Test]
  public function composeDirFromString() {
    $this->assertEquals('META-INF/services/', (new ZipDirEntry('META-INF', 'services'))->getName());
  }

  #[Test]
  public function composeFileFromDirAndString() {
    $this->assertEquals('META-INF/manifest.ini', (new ZipFileEntry(new ZipDirEntry('META-INF'), 'manifest.ini'))->getName());
  }

  #[Test]
  public function composeDirFromDirAndString() {
    $this->assertEquals('META-INF/services/', (new ZipDirEntry(new ZipDirEntry('META-INF'), 'services'))->getName());
  }
}