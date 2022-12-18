<?php namespace io\archive\zip\unittest;

use io\archive\zip\Compression;
use lang\IllegalArgumentException;
use unittest\{Assert, Expect, Test};

class CompressionTest {

  #[Test]
  public function noneInstance() {
    Assert::equals(Compression::$NONE, Compression::getInstance(0));
  }

  #[Test]
  public function gzInstance() {
    Assert::equals(Compression::$GZ, Compression::getInstance(8));
  }

  #[Test]
  public function bzInstance() {
    Assert::equals(Compression::$BZ, Compression::getInstance(12));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function unknownInstance() {
    Compression::getInstance(-1);
  }
}