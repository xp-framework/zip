<?php namespace io\archive\zip\unittest\vendors;

use io\streams\Streams;
use test\{Assert, Test};

/**
 * Tests our own ZIP file implementation.
 *
 * @see   xp://io.archive.zip.ZipFile
 */
class XpZipFileTest extends ZipFileVendorTest {

  /** @return string */
  protected function vendor() { return 'xp'; }

  #[Test]
  public function unicodeZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor(), 'unicode'));
    Assert::equals(1, sizeof($entries));
    Assert::equals(iconv('utf-8', \xp::ENCODING, 'äöü.txt'), $entries[0]->getName());
    Assert::equals(0, $entries[0]->getSize());
    Assert::false($entries[0]->isDirectory());
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
}