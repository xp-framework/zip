<?php namespace io\archive\zip\unittest\vendors;

/**
 * TestCase
 *
 * @see   http://en.wikipedia.org/wiki/JAR_(file_format)
 * @see   http://download.oracle.com/javase/6/docs/technotes/guides/jar/jar.html
 */
class JarFileTest extends ZipFileVendorTest {

  /** @return string */
  protected function vendor() { return 'jar'; }

  #[@test, @ignore('Cannot create empty zipfiles with `jar`')]
  public function emptyZipFile() {
    parent::emptyZipFile();
  }
}
