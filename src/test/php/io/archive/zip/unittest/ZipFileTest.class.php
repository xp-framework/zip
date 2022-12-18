<?php namespace io\archive\zip\unittest;

use io\archive\zip\{ZipArchiveReader, ZipArchiveWriter, ZipFile};
use io\streams\{MemoryInputStream, MemoryOutputStream};
use unittest\{Assert, Test};

class ZipFileTest extends AbstractZipFileTest {

  #[Test]
  public function zipfile_create() {
    Assert::instance(ZipArchiveWriter::class, ZipFile::create(new MemoryOutputStream()));
  }

  #[Test]
  public function zipfile_open() {
    Assert::instance(ZipArchiveReader::class, ZipFile::open(new MemoryInputStream('PK...')));
  }
}