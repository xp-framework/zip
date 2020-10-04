<?php namespace io\archive\zip\unittest\vendors;

use unittest\{Ignore, Test};

/**
 * Tests ZIP file implementation with ZIP files created by
 * PHP's ZipArchive class
 *
 * @see   php://zip
 */
class PHPZipFileTest extends ZipFileVendorTest {
  
  /** @return string */
  protected function vendor() { return 'php'; }

  #[Test, Ignore('Cannot create empty zipfiles with PHP')]
  public function emptyZipFile() {
    parent::emptyZipFile();
  }
}