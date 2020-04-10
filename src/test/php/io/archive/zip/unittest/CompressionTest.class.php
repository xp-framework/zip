<?php namespace io\archive\zip\unittest;

use io\archive\zip\Compression;
use lang\IllegalArgumentException;

/**
 * TestCase for compression enumeration
 *
 * @see      xp://io.archive.zip.Compression
 */
class CompressionTest extends \unittest\TestCase {

  #[@test]
  public function noneInstance() {
    $this->assertEquals(Compression::$NONE, Compression::getInstance(0));
  }

  #[@test]
  public function gzInstance() {
    $this->assertEquals(Compression::$GZ, Compression::getInstance(8));
  }

  #[@test]
  public function bzInstance() {
    $this->assertEquals(Compression::$BZ, Compression::getInstance(12));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function unknownInstance() {
    Compression::getInstance(-1);
  }
}