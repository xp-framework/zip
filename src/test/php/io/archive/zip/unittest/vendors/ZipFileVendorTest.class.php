<?php namespace io\archive\zip\unittest\vendors;

use io\archive\zip\unittest\AbstractZipFileTest;
use test\{Assert, Test};

abstract class ZipFileVendorTest extends AbstractZipFileTest {
  
  /** @return string */
  protected abstract function vendor();
  
  #[Test]
  public function emptyZipFile() {
    Assert::equals([], $this->entriesIn($this->archiveReaderFor($this->vendor(), 'empty')));
  }

  #[Test]
  public function helloZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor(), 'hello'));
    Assert::equals(1, sizeof($entries));
    Assert::equals('hello.txt', $entries[0]->getName());
    Assert::equals(5, $entries[0]->getSize());
    Assert::false($entries[0]->isDirectory());
  }

  #[Test]
  public function umlautZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor(), 'umlaut'));
    Assert::equals(1, sizeof($entries));
    Assert::equals(iconv('utf-8', \xp::ENCODING, 'äöü.txt'), $entries[0]->getName());
    Assert::equals(0, $entries[0]->getSize());
    Assert::false($entries[0]->isDirectory());
  }
}