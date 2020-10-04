<?php namespace io\archive\zip\unittest\vendors;

use io\streams\Streams;
use unittest\actions\ExtensionAvailable;
use unittest\{Action, Ignore, Test};

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
    $this->assertEquals('compression.txt', $entry->getName());
    $this->assertEquals(1660, $entry->getSize());
    
    with ($is= $entry->in()); {
      $this->assertEquals('This file is to be compressed', (string)$is->read(29));
      $is->read(1630);
      $this->assertEquals('.', (string)$is->read(1));
    }
  }

  #[Test, Action(eval: 'new ExtensionAvailable("zlib")')]
  public function deflate() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor(), 'deflate'));
  }

  #[Test, Action(eval: 'new ExtensionAvailable("bz2")')]
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
      $this->assertEquals('password.txt', $entry->getName());
      $this->assertEquals(15, $entry->getSize());
      $this->assertEquals('Secret contents', (string)Streams::readAll($entry->in()));

      $entry= $it->next();
      $this->assertEquals('very.txt', $entry->getName());
      $this->assertEquals(20, $entry->getSize());
      $this->assertEquals('Very secret contents', (string)Streams::readAll($entry->in()));
    }
  }

  #[Test]
  public function zipCryptoPasswordProtected() {
    $this->assertSecuredEntriesIn($this->archiveReaderFor($this->vendor(), 'zip-crypto'));
  }

  #[Test, Ignore('Not yet supported')]
  public function aes256PasswordProtected() {
    $this->assertSecuredEntriesIn($this->archiveReaderFor($this->vendor(), 'aes-256'));
  }
}