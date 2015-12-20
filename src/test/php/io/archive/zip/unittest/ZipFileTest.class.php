<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipArchiveWriter;
use io\archive\zip\ZipArchiveReader;
use io\archive\zip\ZipFile;
use io\streams\MemoryOutputStream;
use io\streams\MemoryInputStream;

class ZipFileTest extends AbstractZipFileTest {

  #[@test]
  public function zipfile_create() {
    $this->assertInstanceOf(ZipArchiveWriter::class, ZipFile::create(new MemoryOutputStream()));
  }

  #[@test]
  public function zipfile_open() {
    $this->assertInstanceOf(ZipArchiveReader::class, ZipFile::open(new MemoryInputStream('PK...')));
  }
}