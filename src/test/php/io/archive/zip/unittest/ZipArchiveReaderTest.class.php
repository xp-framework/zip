<?php namespace io\archive\zip\unittest;

use io\streams\InputStream;

/**
 * Tests ZipArchiveReader class
 *
 * @see   xp://io.archive.zip.ZipArchiveReader
 */
class ZipArchiveReaderTest extends AbstractZipFileTest {

  #[@test]
  public function close() {
    $stream= newinstance(InputStream::class, [], '{
      public $closed= FALSE;
      public function read($limit= 8192) { return ""; }
      public function available() { return 0; }
      public function close() { $this->closed= TRUE; }
    }');
    $reader= new \io\archive\zip\ZipArchiveReader($stream);
    $reader->close();
    $this->assertTrue($stream->closed);
  }
}
