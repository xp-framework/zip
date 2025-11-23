<?php namespace io\archive\zip\unittest\vendors;

use io\streams\Streams;
use lang\IllegalAccessException;
use test\verify\Runtime;
use test\{Assert, Ignore, Expect, Test, Values};

/**
 * Tests 7-ZIP archives
 *
 * @see   http://www.7-zip.org/
 */
class SevenZipFileTest extends ZipFileVendorTest {

  /** @return string */
  protected function vendor() { return '7zip'; }

  /**
   * Assertion helper
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @throws  unittest.AssertionFailedError
   */
  protected function assertCompressedEntryIn($reader) {
    $entry= $reader->iterator()->next();
    Assert::equals('compression.txt', $entry->getName());
    Assert::equals(1660, $entry->getSize());
    
    with ($is= $entry->in()); {
      Assert::equals('This file is to be compressed', (string)$is->read(29));
      $is->read(1630);
      Assert::equals('.', (string)$is->read(1));
    }
  }

  #[Test, Runtime(extensions: ['zlib'])]
  public function deflate() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor(), 'deflate'));
  }

  #[Test, Runtime(extensions: ['bz2'])]
  public function bzip2() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor(), 'bzip2'));
  }

  #[Test, Ignore('Not yet supported')]
  public function deflate64() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor(), 'deflate64'));
  }

  #[Test, Ignore('Not yet supported')]
  public function lzma() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor(), 'lzma'));
  }

  #[Test, Ignore('Not yet supported')]
  public function ppmd() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor(), 'ppmd'));
  }

  #[Test, Expect(IllegalAccessException::class), Values(['zip-crypto', 'aes-128', 'aes-192', 'aes-256'])]
  public function missing_password($fixture) {
    $this->archiveReaderFor($this->vendor(), $fixture)
      ->iterator()
      ->next()
      ->in()
    ;
  }

  #[Test, Expect(IllegalAccessException::class), Values(['zip-crypto', 'aes-128', 'aes-192', 'aes-256'])]
  public function incorrect_password($fixture) {
    $this->archiveReaderFor($this->vendor(), $fixture)
      ->usingPassword('wrong')
      ->iterator()
      ->next()
      ->in()
    ;
  }

  #[Test, Values(['zip-crypto', 'aes-128', 'aes-192', 'aes-256'])]
  public function password_not_needed_for_listing($fixture) {
    $reader= $this->archiveReaderFor($this->vendor(), $fixture);
    $sizes= [];
    foreach ($reader->entries() as $entry) {
      $sizes[$entry->getName()]= $entry->getSize();
    }

    Assert::equals(['password.txt' => 15, 'very.txt' => 20], $sizes);
  }

  #[Test, Values(['zip-crypto', 'aes-128', 'aes-192', 'aes-256'])]
  public function password_protected($fixture) {
    $reader= $this->archiveReaderFor($this->vendor(), $fixture)->usingPassword('secret');
    $contents= [];
    foreach ($reader->entries() as $entry) {
      $contents[$entry->getName()]= Streams::readAll($entry->in());
    }

    Assert::equals(
      ['password.txt' => 'Secret contents', 'very.txt' => 'Very secret contents'],
      $contents
    );
  }
}