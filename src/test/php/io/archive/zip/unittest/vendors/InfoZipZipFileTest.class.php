<?php namespace io\archive\zip\unittest\vendors;

/**
 * Tests ZIP file implementation with ZIP files created by the
 * "zip" command line utility (Info-ZIP)
 *
 */
class InfoZipZipFileTest extends ZipFileVendorTest {
  
  /** @return string */
  protected function vendorName() { return 'infozip'; }
}
