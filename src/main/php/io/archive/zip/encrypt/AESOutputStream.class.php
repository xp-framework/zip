<?php namespace io\archive\zip\encrypt;

use io\streams\OutputStream;

/**
 * Encrypts using little-endian variant of AES-CTR
 *
 * @ext   openssl
 */
class AESOutputStream implements OutputStream {
  const BLOCK = 16;

  private $out, $key;
  private $cipher, $counter, $hmac;
  private $buffer= '';

  /**
   * Constructor
   *
   * @param  io.streams.OutputStream $out
   * @param  string $key
   * @param  string $auth
   */
  public function __construct($out, $key, $auth) {
    $this->out= $out;
    $this->key= $key;

    $this->cipher= 'aes-'.(strlen($key) * 8).'-ecb';
    $this->counter= "\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
    $this->hmac= hash_init('sha1', HASH_HMAC, $auth);
  }

  /**
   * Encrypt, updating HMAC and the counter while doing so
   *
   * @param  string $input plaintext
   * @return string
   */
  private function encrypt($input) {
    $return= '';
    for ($offset= 0, $l= strlen($input); $offset < $l; $offset+= self::BLOCK) {

      // Encrypt counter block using AES-ECB
      $keystream= openssl_encrypt(
        $this->counter,
        $this->cipher,
        $this->key,
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
      );

      // XOR relevant part
      $return.= substr($input, $offset, self::BLOCK) ^ $keystream;

      // Increment little-endian counter
      for ($j= 0, $carry= 1; $j < 16 && $carry; $j++) {
        $s= ord($this->counter[$j]) + $carry;
        $this->counter[$j]= chr($s & 0xFF);
        $carry = $s >> 8;
      }
    }

    hash_update($this->hmac, $return);
    return $return;
  }

  /**
   * Write plaintext to the stream
   *
   * @param  string $bytes
   * @return void
   */
  public function write($bytes) {
    $chunk= $this->buffer.$bytes;

    // Write only full AES blocks
    $full= -strlen($chunk) % self::BLOCK;
    if ($full > 0) {
      $this->out->write($this->encrypt(substr($chunk, 0, $full)));
      $this->buffer= substr($chunk, $full);
    } else {
      $this->buffer= $chunk;
    }
  }

  /**
   * Flush
   *
   * @return void
   */
  public function flush() {
    // NOOP
  }

  /**
   * Close the stream, flushing the last partial block and appending HMAC
   *
   * @return void
   */
  public function close() {

    // Encrypt final partial block
    if (strlen($this->buffer)) {
      $this->out->write($this->encrypt($this->buffer));
    }

    // Write 10 bytes of HMAC-SHA1
    $mac= hash_final($this->hmac, true);
    $this->out->write(substr($mac, 0, 10));

    $this->buffer= '';
    $this->out->close();
  }
}