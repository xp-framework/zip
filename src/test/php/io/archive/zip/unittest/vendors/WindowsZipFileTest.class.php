<?php namespace io\archive\zip\unittest\vendors;



/**
 * Tests ZIP file implementation with ZIP files created by the
 * Windows built-in ZIP file support.
 *
 */
class WindowsZipFileTest extends ZipFileVendorTest {
  
  /**
   * Returns vendor name
   *
   * @return  string
   */
  protected function vendorName() {
    return 'windows';
  }
}
