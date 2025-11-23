<?php namespace io\archive\zip;

use io\streams\InputStream;
use lang\IllegalStateException;

/**
 * Deciphers using little-endian variant of AES-CTR
 *
 * @ext   openssl
 */
class AESInputStream implements InputStream {
  const BLOCK= 16;

  private $in, $key;
  private $cipher, $counter, $hmac;
  private $buffer= '';

  /**
   * Constructor
   *
   * @param  io.streams.InputStream $in
   * @param  string $key
   * @param  string $auth
   */
  public function __construct($in, $key, $auth) {
    $this->in= $in;
    $this->key= $key;

    $this->cipher= 'aes-'.(strlen($key) * 8).'-ecb';
    $this->counter= "\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
    $this->hmac= hash_init('sha1', HASH_HMAC, $auth);
  }

  /**
   * Decrypt, updating the HMAC and the counter while doing so
   *
   * @param  string $input
   * @return string
   */
  private function decrypt($input) {
    hash_update($this->hmac, $input);

    $return= '';
    for ($offset= 0, $l= strlen($input); $offset < $l; $offset+= self::BLOCK) {

      // Encrypt counter block using AES-ECB
      $keystream= openssl_encrypt(
        $this->counter,
        $this->cipher,
        $this->key,
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
      );

      // Take relevant part
      $return.= substr($input, $offset, self::BLOCK) ^ $keystream;

      // Increment little-endian counter
      for ($j= 0, $carry= 1; $j < 16 && $carry; $j++) {
        $s= ord($this->counter[$j]) + $carry;
        $this->counter[$j]= chr($s & 0xff);
        $carry= $s >> 8;
      }
    }
    return $return;
  }

  /**
   * Read a string
   *
   * @param  int $limit default 8192
   * @return string
   * @throws lang.IllegalStateException when HMAC verification fails
   */
  public function read($limit= 8192) {
    $chunk= $this->buffer.$this->in->read($limit);

    // Ensure we always decrypt complete blocks while streaming
    if ($this->in->available()) {
      $rest= -strlen($chunk) % self::BLOCK;
      if ($rest) {
        $this->buffer= substr($chunk, $rest);
        return $this->decrypt(substr($chunk, 0, $rest));
      } else {
        $this->buffer= '';
        return $this->decrypt($chunk);
      }
    }

    // Verify HMAC checksum for last block
    $this->buffer= '';
    $plain= $this->decrypt(substr($chunk, 0, -10));

    $mac= hash_final($this->hmac, true);
    if (0 !== substr_compare($mac, substr($chunk, -10), 0, 10)) {
      throw new IllegalStateException('HMAC verification failed — corrupted data');
    }

    return $plain;
  }

  /**
   * Returns the number of bytes that can be read from this stream 
   * without blocking.
   * 
   * @return int
   */
  public function available() {
    return $this->in->available();
  }

  /**
   * Close this buffer
   *
   * @return void
   */
  public function close() {
    $this->in->close();
  }
}