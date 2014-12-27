<?php namespace io\archive\zip\unittest\vendors;

/**
 * Abstract base class
 */
abstract class ZipFileVendorTest extends \io\archive\zip\unittest\AbstractZipFileTest {
  
  /**
   * Returns vendor name
   *
   * @return  string
   */
  protected abstract function vendor();
  
  #[@test]
  public function emptyZipFile() {
    $this->assertEquals(array(), $this->entriesIn($this->archiveReaderFor($this->vendor(), 'empty')));
  }

  #[@test]
  public function helloZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor(), 'hello'));
    $this->assertEquals(1, sizeof($entries));
    $this->assertEquals('hello.txt', $entries[0]->getName());
    $this->assertEquals(5, $entries[0]->getSize());
    $this->assertFalse($entries[0]->isDirectory());
  }

  #[@test]
  public function umlautZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor(), 'umlaut'));
    $this->assertEquals(1, sizeof($entries));
    $this->assertEquals(iconv('utf-8', \xp::ENCODING, 'äöü.txt'), $entries[0]->getName());
    $this->assertEquals(0, $entries[0]->getSize());
    $this->assertFalse($entries[0]->isDirectory());
  }
}
