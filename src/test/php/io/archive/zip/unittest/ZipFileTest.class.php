<?php namespace io\archive\zip\unittest;

use io\archive\zip\{ZipArchiveReader, ZipArchiveWriter, ZipFile};
use io\streams\{MemoryInputStream, MemoryOutputStream};
use unittest\Test;

class ZipFileTest extends AbstractZipFileTest {

  #[Test]
  public function zipfile_create() {
    $this->assertInstanceOf(ZipArchiveWriter::class, ZipFile::create(new MemoryOutputStream()));
  }

  #[Test]
  public function zipfile_open() {
    $this->assertInstanceOf(ZipArchiveReader::class, ZipFile::open(new MemoryInputStream('PK...')));
  }
}