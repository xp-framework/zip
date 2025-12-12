<?php namespace io\archive\zip\encrypt;

use io\archive\zip\{ZipCipher, Encryption};
use lang\{IllegalAccessException, IllegalArgumentException};
use util\Secret;

class Cipher extends Encryption {
  private $password;
  private $cipher= null;

  public function __construct($password) {
    if ($password instanceof Secret) {
      $this->password= $password;
    } else if (null !== $password) {
      $this->password= new Secret($password);
    }
  }

  public function in($stream, $crc32) {
    if (null === $this->password) {
      throw new IllegalAccessException('Missing password');
    }

    $this->cipher ?? $this->cipher= new ZipCipher($this->password->reveal());
    $preamble= $this->cipher->decipher($stream->read(12));
    if (ord($preamble[11]) !== (($crc32 >> 24) & 0xff)) {
      throw new IllegalAccessException('The password did not match');
    }

    return new DecipheringInputStream($stream, $this->cipher);
  }

  public function header($writer, $compression, $modified, $crc32, $compressed, $size, $name) {
    $writer->addEntry(
      1,
      $compression,
      $modified,
      $crc32,
      $compressed + 12,
      $size,
      $name,
      ''
    );
  }

  public function out($writer, $crc32) {
    $this->cipher ?? $this->cipher= new ZipCipher($this->password->reveal());
    $writer->stream->write($this->cipher->cipher(random_bytes(11).chr(($crc32 >> 24) & 0xff)));

    return new CipheringOutputStream($writer->stream, $this->cipher);
  }
}