<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipArchiveReader;
use io\streams\InputStream;
use lang\IllegalStateException;
use test\{Assert, Test};

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
  public function load_content_after_iteration() {
    $reader= new ZipArchiveReader($this->randomAccess('fixtures', 'twofiles'));
    $entries= iterator_to_array($reader->entries());

    Assert::equals('Eins', $this->entryContent($entries[0]));
    Assert::equals('Zwei', $this->entryContent($entries[1]));
  }

  #[Test]
  public function load_content_twice_from_seekable() {
    $reader= new ZipArchiveReader($this->randomAccess('fixtures', 'onefile'));
    $entry= $reader->iterator()->next();

    Assert::equals('World', $this->entryContent($entry));
    Assert::equals('World', $this->entryContent($entry));
  }

  #[Test]
  public function load_content_twice_from_unseekable() {
    $reader= new ZipArchiveReader($this->sequentialAccess('fixtures', 'onefile'));
    $entry= $reader->iterator()->next();

    Assert::equals('World', $this->entryContent($entry));
    Assert::throws(IllegalStateException::class, function() use($entry) {
      $this->entryContent($entry);
    });
  }
}