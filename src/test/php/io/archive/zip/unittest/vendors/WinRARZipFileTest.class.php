<?php namespace io\archive\zip\unittest\vendors;

/**
 * Tests ZIP file implementation with ZIP files created by
 * WinRAR
 *
 * @see   http://www.winrar.de/
 */
class WinRARZipFileTest extends ZipFileVendorTest {

  /** @return string */
  protected function vendorName() { return 'winrar'; }

  /**
   * Tests reading an empty zipfile
   *
   */
  #[@test, @ignore('Cannot create empty zipfiles with WinRAR')]
  public function emptyZipFile() {
    parent::emptyZipFile();
  }
}
