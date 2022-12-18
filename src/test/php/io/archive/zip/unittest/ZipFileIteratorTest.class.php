<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipEntry;
use unittest\{Assert, Expect, Test};
use util\NoSuchElementException;

/**
 * Base class for testing zip file contents
 *
 * @see  io.archive.zip.ZipArchiveReader::entries
 */
class ZipFileIteratorTest extends ZipFileContentsTest {

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @return  [:string] content
   */
  protected function entriesWithContentIn(\io\archive\zip\ZipArchiveReader $zip) {
    $entries= [];
    for ($it= $zip->iterator(); $it->hasNext(); ) {
      $entry= $it->next();
      $entries[$entry->getName()]= $this->entryContent($entry);
    }
    return $entries;
  }

  #[Test]
  public function emptyFilesHasNoEntries() {
    Assert::false($this->archiveReaderFor('fixtures', 'nofiles')->iterator()->hasNext());
  }

  #[Test, Expect(NoSuchElementException::class)]
  public function iterationOverEndForEmpty() {
    $this->archiveReaderFor('fixtures', 'nofiles')->iterator()->next();
  }

  #[Test]
  public function iterationOverEnd() {
    $it= $this->archiveReaderFor('fixtures', 'onefile')->iterator();
    $it->next();
    try {
      $it->next();
      $this->fail('Expected exception not thrown', null, 'util.NoSuchElementException');
    } catch (NoSuchElementException $expected) { }
    Assert::false($it->hasNext());
  }

  #[Test]
  public function iterator() {
    $it= $this->archiveReaderFor('fixtures', 'onefile')->iterator();
    Assert::true($it->hasNext());
    Assert::instance(ZipEntry::class, $it->next());
    Assert::false($it->hasNext());
  }
}