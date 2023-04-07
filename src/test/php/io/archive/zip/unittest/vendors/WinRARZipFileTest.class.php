<?php namespace io\archive\zip\unittest\vendors;

use test\{Ignore, Test};

/**
 * Tests ZIP file implementation with ZIP files created by
 * WinRAR
 *
 * @see   http://www.winrar.de/
 */
class WinRARZipFileTest extends ZipFileVendorTest {

  /** @return string */
  protected function vendor() { return 'winrar'; }

  /**
   * Tests reading an empty zipfile
   *
   */
  #[Test, Ignore('Cannot create empty zipfiles with WinRAR')]
  public function emptyZipFile() {
    parent::emptyZipFile();
  }
}