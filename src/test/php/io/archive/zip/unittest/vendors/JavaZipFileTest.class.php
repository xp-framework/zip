<?php namespace io\archive\zip\unittest\vendors;

/**
 * Tests ZIP file implementation with ZIP files created by
 * Java's java.util.zip API
 *
 * @see   http://java.sun.com/javase/6/docs/api/java/util/zip/package-summary.html
 */
class JavaZipFileTest extends ZipFileVendorTest {

  /** @return string */
  protected function vendor() { return 'java'; }

  #[@test, @ignore('Cannot create empty zipfiles with java.util.zip')]
  public function emptyZipFile() {
    parent::emptyZipFile();
  }
}
