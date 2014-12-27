<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipFile;
use io\streams\MemoryOutputStream;
use io\streams\MemoryInputStream;

class ZipFileFactoryTest extends AbstractZipFileTest {

  #[@test]
  public function zipfile_create() {
    $this->assertInstanceOf('io.archive.zip.ZipArchiveWriter', ZipFile::create(new MemoryOutputStream()));
  }

  #[@test]
  public function zipfile_open() {
    $this->assertInstanceOf('io.archive.zip.ZipArchiveReader', ZipFile::open(new MemoryInputStream('PK...')));
  }
}