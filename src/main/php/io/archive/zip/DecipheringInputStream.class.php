<?php namespace io\archive\zip;

use io\streams\InputStream;

/**
 * Deciphers using ZipCipher class
 *
 * @see   xp://io.archive.zip.ZipCipher
 */
class DecipheringInputStream extends \lang\Object implements InputStream {
  protected $in= null;
  protected $cipher= null;

  /**
   * Constructor
   *
   * @param   io.streams.InputStream in
   * @param   io.archive.zip.ZipCipher cipher
   */
  public function __construct($in, $cipher) {
    $this->in= $in;
    $this->cipher= $cipher;
  }

  /**
   * Read a string
   *
   * @param   int limit default 8192
   * @return  string
   */
  public function read($limit= 8192) {
    return $this->cipher->decipher($this->in->read($limit));
  }

  /**
   * Returns the number of bytes that can be read from this stream 
   * without blocking.
   *
   */
  public function available() {
    return $this->in->available();
  }

  /**
   * Close this buffer
   *
   */
  public function close() {
    $this->in->close();
  }
}
