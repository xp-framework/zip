<?php namespace io\archive\zip\unittest;

use io\streams\InputStream;
use unittest\Test;

/**
 * Tests ZipArchiveReader class
 *
 * @see   xp://io.archive.zip.ZipArchiveReader
 */
class ZipArchiveReaderTest extends AbstractZipFileTest {

  #[Test]
  public function close() {
    $stream= new class() implements InputStream {
      public $closed= false;
      public function read($limit= 8192) { return ''; }
      public function available() { return 0; }
      public function close() { $this->closed= true; }
    };
    $reader= new \io\archive\zip\ZipArchiveReader($stream);
    $reader->close();
    $this->assertTrue($stream->closed);
  }
}