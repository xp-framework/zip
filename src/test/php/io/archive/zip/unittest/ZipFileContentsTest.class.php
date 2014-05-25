<?php namespace io\archive\zip\unittest;

use io\streams\Streams;

/**
 * Base class for testing zip file contents
 *
 * @see   xp://net.xp_framework.unittest.io.archive.MalformedZipFileTest
 * @see   xp://net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest
 */
abstract class ZipFileContentsTest extends ZipFileTest {

  /**
   * Returns entry content
   *
   * @param   io.archive.zip.ZipEntry entry
   * @return  string
   */
  protected function entryContent(\io\archive\zip\ZipEntry $entry) {
    if ($entry->isDirectory()) {
      return null;
    } else {
      return (string)Streams::readAll($entry->getInputStream());
    }
  }

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @return  [:string] content
   */
  protected abstract function entriesWithContentIn(\io\archive\zip\ZipArchiveReader $zip);

  #[@test]
  public function nofiles() {
    $this->assertEquals(
      array(),
      $this->entriesWithContentIn($this->archiveReaderFor('fixtures', 'nofiles'))
    );
  }

  #[@test]
  public function onefile() {
    $this->assertEquals(
      array('hello.txt' => 'World'),
      $this->entriesWithContentIn($this->archiveReaderFor('fixtures', 'onefile'))
    );
  }

  #[@test]
  public function onedir() {
    $this->assertEquals(
      array('dir/' => null),
      $this->entriesWithContentIn($this->archiveReaderFor('fixtures', 'onedir'))
    );
  }

  #[@test]
  public function twofiles() {
    $this->assertEquals(
      array('one.txt' => 'Eins', 'two.txt' => 'Zwei'),
      $this->entriesWithContentIn($this->archiveReaderFor('fixtures', 'twofiles'))
    );
  }

  #[@test]
  public function loadContentAfterIteration() {
    $entries= $this->entriesIn($this->archiveReaderFor('fixtures', 'twofiles'));
    $this->assertEquals('Eins', $this->entryContent($entries[0]));
    $this->assertEquals('Zwei', $this->entryContent($entries[1]));
  }
}
