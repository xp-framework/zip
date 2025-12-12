<?php namespace io\archive\zip\encrypt;

use io\archive\zip\ZipCipher;
use io\streams\InputStream;

/** Deciphers using ZipCipher class */
class DecipheringInputStream implements InputStream {
  private $in, $cipher;

  /** Constructor */
  public function __construct(InputStream $in, ZipCipher $cipher) {
    $this->in= $in;
    $this->cipher= $cipher;
  }

  /**
   * Read a string
   *
   * @param  int $limit
   * @return string
   */
  public function read($limit= 8192) {
    return $this->cipher->decipher($this->in->read($limit));
  }

  /** @return int */
  public function available() {
    return $this->in->available();
  }

  /** @return void */
  public function close() {
    $this->in->close();
  }
}