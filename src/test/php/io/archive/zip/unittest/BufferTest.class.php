<?php namespace io\archive\zip\unittest;

use io\archive\zip\Buffer;
use test\{Assert, Test, Values};

class BufferTest {

  #[Test]
  public function can_create() {
    new Buffer();
  }

  #[Test]
  public function length_initial() {
    $fixture= new Buffer();
    Assert::equals(0, $fixture->length);
  }

  #[Test, Values(['', 'Test'])]
  public function length_after_writing($bytes) {
    $fixture= new Buffer();
    $fixture->write($bytes);
    Assert::equals(strlen($bytes), $fixture->length);
  }

  #[Test, Values(['', 'Test'])]
  public function available_after_writing($bytes) {
    $fixture= new Buffer();
    $fixture->write($bytes);
    Assert::equals(strlen($bytes), $fixture->available());
  }

  #[Test, Values([1, 2, 3, 4])]
  public function available_after_reading($n) {
    $fixture= new Buffer();
    $fixture->write('Test');
    $fixture->read($n);
    Assert::equals(4 - $n, $fixture->available());
  }

  #[Test, Values(['', 'Test'])]
  public function read_after_writing($bytes) {
    $fixture= new Buffer();
    $fixture->write($bytes);
    Assert::equals($bytes, $fixture->read());
  }
}