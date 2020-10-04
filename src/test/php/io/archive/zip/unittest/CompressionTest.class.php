<?php namespace io\archive\zip\unittest;

use io\archive\zip\Compression;
use lang\IllegalArgumentException;
use unittest\{Expect, Test};

/**
 * TestCase for compression enumeration
 *
 * @see      xp://io.archive.zip.Compression
 */
class CompressionTest extends \unittest\TestCase {

  #[Test]
  public function noneInstance() {
    $this->assertEquals(Compression::$NONE, Compression::getInstance(0));
  }

  #[Test]
  public function gzInstance() {
    $this->assertEquals(Compression::$GZ, Compression::getInstance(8));
  }

  #[Test]
  public function bzInstance() {
    $this->assertEquals(Compression::$BZ, Compression::getInstance(12));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function unknownInstance() {
    Compression::getInstance(-1);
  }
}