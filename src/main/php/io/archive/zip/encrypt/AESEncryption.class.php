<?php namespace io\archive\zip\encrypt;

use io\archive\zip\Encryption;
use lang\{IllegalAccessException, IllegalArgumentException};
use util\Secret;

class AESEncryption extends Encryption {
  private $password, $st, $sl, $dl;

  public function __construct($password, $bits) {
    if ($password instanceof Secret) {
      $this->password= $password;
    } else if (null !== $password) {
      $this->password= new Secret($password);
    }

    switch ($bits) {
      case 128: case 1: $this->st= 1; $this->sl= 8; $this->dl= 16; break;
      case 192: case 2: $this->st= 2; $this->sl= 12; $this->dl= 24; break;
      case 256: case 3: $this->st= 3; $this->sl= 16; $this->dl= 32; break;
      default: throw new IllegalArgumentException('Invalid AES bits '.$bits);
    }
  }

  public function in($stream, $crc32) {
    if (null === $this->password) {
      throw new IllegalAccessException('Missing password');
    }

    $salt= $stream->read($this->sl);
    $pvv= $stream->read(2);
    $dk= hash_pbkdf2('sha1', $this->password->reveal(), $salt, 1000, 2 * $this->dl + 2, true);
    if (0 !== substr_compare($dk, $pvv, 2 * $this->dl, 2)) {
      throw new IllegalAccessException('The password did not match');
    }

    return new AESInputStream($stream, substr($dk, 0, $this->dl), substr($dk, $this->dl, $this->dl));
  }

  public function header($writer, $compression, $modified, $crc32, $compressed, $size, $name) {
    $writer->addEntry(
      0,
      99,
      $modified,
      $crc32,
      $compressed + $this->sl + 2 + 10,
      $size,
      $name,
      pack(
        'vvva2cv',
        0x9901,    // Header ID
        7,         // Data Size
        2,         // Version
        'AE',      // Vendor
        $this->st, // Strength
        $compression
      )
    );
  }

  public function out($writer, $crc32) {
    $salt= random_bytes($this->sl);
    $dk= hash_pbkdf2('sha1', $this->password->reveal(), $salt, 1000, 2 * $this->dl + 2, true);
    $writer->stream->write($salt.substr($dk, -2));

    return new AESOutputStream($writer->stream, substr($dk, 0, $this->dl), substr($dk, $this->dl, $this->dl));
  }
}