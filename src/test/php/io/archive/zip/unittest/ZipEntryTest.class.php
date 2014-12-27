<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipFileEntry;
use io\archive\zip\ZipDirEntry;

/**
 * TestCase
 *
 * @see     xp://io.archive.zip.ZipFileEntry
 * @see     xp://io.archive.zip.ZipDirEntry
 */
class ZipEntryTest extends AbstractZipFileTest {

  #[@test]
  public function simpleFileName() {
    $this->assertEquals('Hello.txt', create(new ZipFileEntry('Hello.txt'))->getName());
  }

  #[@test]
  public function simpleDirName() {
    $this->assertEquals('Hello/', create(new ZipDirEntry('Hello'))->getName());
  }

  #[@test]
  public function backslashesReplacedInFile() {
    $this->assertEquals('hello/World.txt', create(new ZipFileEntry('hello\\World.txt'))->getName());
  }

  #[@test]
  public function backslashesReplacedInDir() {
    $this->assertEquals('hello/World/', create(new ZipDirEntry('hello\\World'))->getName());
  }

  #[@test]
  public function trailingSlashesInDirNormalized() {
    $this->assertEquals('hello/', create(new ZipDirEntry('hello//'))->getName());
  }

  #[@test]
  public function trailingBackslashesInDirNormalized() {
    $this->assertEquals('hello/', create(new ZipDirEntry('hello\\\\'))->getName());
  }

  #[@test]
  public function composeFileFromString() {
    $this->assertEquals('META-INF/manifest.ini', create(new ZipFileEntry('META-INF', 'manifest.ini'))->getName());
  }

  #[@test]
  public function composeDirFromString() {
    $this->assertEquals('META-INF/services/', create(new ZipDirEntry('META-INF', 'services'))->getName());
  }

  #[@test]
  public function composeFileFromDirAndString() {
    $this->assertEquals('META-INF/manifest.ini', create(new ZipFileEntry(new ZipDirEntry('META-INF'), 'manifest.ini'))->getName());
  }

  #[@test]
  public function composeDirFromDirAndString() {
    $this->assertEquals('META-INF/services/', create(new ZipDirEntry(new ZipDirEntry('META-INF'), 'services'))->getName());
  }
}
