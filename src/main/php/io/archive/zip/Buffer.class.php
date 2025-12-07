<?php namespace io\archive\zip;

use io\streams\{InputStream, OutputStream};

// TODO: Buffer to disk!
class Buffer implements InputStream, OutputStream {
  public $length= 0;
  private $position= 0;
  private $bytes= '';

  /** @return int */
  public function available() {
    return $this->length - $this->position;
  }

  /**
   * Read a number of given bytes
   *
   * @param  int $size
   * @return string
   */
  public function read($size= 8192) {
    $chunk= substr($this->bytes, $this->position, $size);
    $this->position+= strlen($chunk);
    return $chunk;
  }

  /**
   * Write given bytes
   *
   * @param  string $bytes
   * @return void
   */
  public function write($bytes) {
    $this->length+= strlen($bytes);
    $this->bytes.= $bytes;
  }

  /** @return void */
  public function flush() {
    // NOOP
  }

  /** @return void */
  public function close() {
    // NOOP
  }
}