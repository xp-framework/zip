<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipArchiveReader;
use io\streams\InputStream;
use unittest\{Assert, Test};

class ZipArchiveReaderTest extends AbstractZipFileTest {

  #[Test]
  public function close() {
    $stream= new class() implements InputStream {
      public $closed= false;
      public function read($limit= 8192) { return ''; }
      public function available() { return 0; }
      public function close() { $this->closed= true; }
    };
    $reader= new ZipArchiveReader($stream);
    $reader->close();
    Assert::true($stream->closed);
  }
}