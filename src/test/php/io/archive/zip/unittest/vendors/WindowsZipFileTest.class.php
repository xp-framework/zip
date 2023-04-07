<?php namespace io\archive\zip\unittest\vendors;

use test\Assert;
/**
 * Tests ZIP file implementation with ZIP files created by the
 * Windows built-in ZIP file support.
 *
 */
class WindowsZipFileTest extends ZipFileVendorTest {

  /** @return string */
  protected function vendor() { return 'windows'; }
}