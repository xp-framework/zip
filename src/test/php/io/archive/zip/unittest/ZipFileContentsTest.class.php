<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipArchiveReader;
use io\streams\Streams;
use unittest\{Assert, Test};

/**
 * Base class for testing zip file contents
 *
 * @see   xp://net.xp_framework.unittest.io.archive.MalformedZipFileTest
 * @see   xp://net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest
 */
abstract class ZipFileContentsTest extends AbstractZipFileTest {

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @return  [:string] content
   */
  protected abstract function entriesWithContentIn(ZipArchiveReader $zip);

  #[Test]
  public function nofiles() {
    Assert::equals(
      [],
      $this->entriesWithContentIn($this->archiveReaderFor('fixtures', 'nofiles'))
    );
  }

  #[Test]
  public function onefile() {
    Assert::equals(
      ['hello.txt' => 'World'],
      $this->entriesWithContentIn($this->archiveReaderFor('fixtures', 'onefile'))
    );
  }

  #[Test]
  public function onedir() {
    Assert::equals(
      ['dir/' => null],
      $this->entriesWithContentIn($this->archiveReaderFor('fixtures', 'onedir'))
    );
  }

  #[Test]
  public function twofiles() {
    Assert::equals(
      ['one.txt' => 'Eins', 'two.txt' => 'Zwei'],
      $this->entriesWithContentIn($this->archiveReaderFor('fixtures', 'twofiles'))
    );
  }

  #[Test]
  public function loadContentAfterIteration() {
    $entries= $this->entriesIn($this->archiveReaderFor('fixtures', 'twofiles'));
    Assert::equals('Eins', $this->entryContent($entries[0]));
    Assert::equals('Zwei', $this->entryContent($entries[1]));
  }
}