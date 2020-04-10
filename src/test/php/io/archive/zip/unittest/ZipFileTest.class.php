<?php namespace io\archive\zip\unittest;

use io\archive\zip\{ZipArchiveReader, ZipArchiveWriter, ZipFile};
use io\streams\{MemoryInputStream, MemoryOutputStream};

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