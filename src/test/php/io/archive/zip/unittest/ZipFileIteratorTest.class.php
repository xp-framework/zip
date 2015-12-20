<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipEntry;
use util\NoSuchElementException;

/**
 * Base class for testing zip file contents
 *
 * @see      xp://io.archive.zip.ZipArchiveReader#entries
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

  #[@test]
  public function emptyFilesHasNoEntries() {
    $this->assertFalse($this->archiveReaderFor('fixtures', 'nofiles')->iterator()->hasNext());
  }

  #[@test, @expect(NoSuchElementException::class)]
  public function iterationOverEndForEmpty() {
    $this->archiveReaderFor('fixtures', 'nofiles')->iterator()->next();
  }

  #[@test]
  public function iterationOverEnd() {
    $it= $this->archiveReaderFor('fixtures', 'onefile')->iterator();
    $it->next();
    try {
      $it->next();
      $this->fail('Expected exception not thrown', null, 'util.NoSuchElementException');
    } catch (NoSuchElementException $expected) { }
    $this->assertFalse($it->hasNext());
  }

  #[@test]
  public function iterator() {
    $it= $this->archiveReaderFor('fixtures', 'onefile')->iterator();
    $this->assertTrue($it->hasNext());
    $this->assertInstanceOf(ZipEntry::class, $it->next());
    $this->assertFalse($it->hasNext());
  }
}
