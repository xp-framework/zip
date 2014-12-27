<?php namespace io\archive\zip\unittest\vendors;

/**
 * Tests ZIP file implementation with ZIP files created by
 * PHP's ZipArchive class
 *
 * @see   php://zip
 */
class PHPZipFileTest extends ZipFileVendorTest {
  
  /** @return string */
  protected function vendor() { return 'php'; }

  #[@test, @ignore('Cannot create empty zipfiles with PHP')]
  public function emptyZipFile() {
    parent::emptyZipFile();
  }
}
