<?php namespace io\archive\zip\unittest\vendors;

use io\streams\Streams;
use test\verify\Runtime;
use test\{Assert, Ignore, Test};

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

  /**
   * Assertion helper
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @throws  unittest.AssertionFailedError
   */
  protected function assertSecuredEntriesIn($reader) {
    with ($it= $reader->usingPassword('secret')->iterator()); {
      $entry= $it->next();
      Assert::equals('password.txt', $entry->getName());
      Assert::equals(15, $entry->getSize());
      Assert::equals('Secret contents', (string)Streams::readAll($entry->in()));

      $entry= $it->next();
      Assert::equals('very.txt', $entry->getName());
      Assert::equals(20, $entry->getSize());
      Assert::equals('Very secret contents', (string)Streams::readAll($entry->in()));
    }
  }

  #[Test]
  public function zipCryptoPasswordProtected() {
    $this->assertSecuredEntriesIn($this->archiveReaderFor($this->vendor(), 'zip-crypto'));
  }

  #[Test]
  public function aes256PasswordProtected() {
    $this->assertSecuredEntriesIn($this->archiveReaderFor($this->vendor(), 'aes-256'));
  }
}