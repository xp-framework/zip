<?php namespace io\archive\zip\encrypt;

use io\archive\zip\ZipCipher;
use io\streams\OutputStream;

/** Ciphers using ZipCipher class */
class CipheringOutputStream implements OutputStream {
  private $out, $cipher;
  
  /** Constructor */
  public function __construct(OutputStream $out, ZipCipher $cipher) {
    $this->out= $out;
    $this->cipher= $cipher;
  }

  /**
   * Write a string
   *
   * @param  var $arg
   */
  public function write($arg) {
    $this->out->write($this->cipher->cipher($arg));
  }

  /** @return void */
  public function flush() { }

  /** @return void */
  public function close() { }
}