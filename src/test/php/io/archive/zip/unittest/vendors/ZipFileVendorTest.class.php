<?php namespace io\archive\zip\unittest\vendors;

use io\archive\zip\unittest\AbstractZipFileTest;
use unittest\Test;

abstract class ZipFileVendorTest extends AbstractZipFileTest {
  
  /** @return string */
  protected abstract function vendor();
  
  #[Test]
  public function emptyZipFile() {
    $this->assertEquals([], $this->entriesIn($this->archiveReaderFor($this->vendor(), 'empty')));
  }

  #[Test]
  public function helloZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor(), 'hello'));
    $this->assertEquals(1, sizeof($entries));
    $this->assertEquals('hello.txt', $entries[0]->getName());
    $this->assertEquals(5, $entries[0]->getSize());
    $this->assertFalse($entries[0]->isDirectory());
  }

  #[Test]
  public function umlautZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor(), 'umlaut'));
    $this->assertEquals(1, sizeof($entries));
    $this->assertEquals(iconv('utf-8', \xp::ENCODING, 'äöü.txt'), $entries[0]->getName());
    $this->assertEquals(0, $entries[0]->getSize());
    $this->assertFalse($entries[0]->isDirectory());
  }
}