<?php namespace io\archive\zip\unittest\vendors;

use io\streams\Streams;

/**
 * Tests our own ZIP file implementation.
 *
 * @see   xp://io.archive.zip.ZipFile
 */
class XpZipFileTest extends ZipFileVendorTest {

  /** @return string */
  protected function vendor() { return 'xp'; }

  #[@test]
  public function unicodeZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor(), 'unicode'));
    $this->assertEquals(1, sizeof($entries));
    $this->assertEquals(iconv('utf-8', \xp::ENCODING, 'äöü.txt'), $entries[0]->getName());
    $this->assertEquals(0, $entries[0]->getSize());
    $this->assertFalse($entries[0]->isDirectory());
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
      $this->assertEquals('Secret contents', (string)Streams::readAll($entry->getInputStream()));

      $entry= $it->next();
      $this->assertEquals('very.txt', $entry->getName());
      $this->assertEquals(20, $entry->getSize());
      $this->assertEquals('Very secret contents', (string)Streams::readAll($entry->getInputStream()));
    }
  }

  #[@test]
  public function zipCryptoPasswordProtected() {
    $this->assertSecuredEntriesIn($this->archiveReaderFor($this->vendor(), 'zip-crypto'));
  }
}
