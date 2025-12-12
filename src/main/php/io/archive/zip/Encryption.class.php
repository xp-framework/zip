<?php namespace io\archive\zip;

use io\archive\zip\encrypt\{AESEncryption, Cipher};

/** Encryption methods */
abstract class Encryption {

  /**
   * Returns AES encryption with either 128, 192 or 256 bits
   *
   * @ext    openssl
   * @param  string|util.Secret $password
   * @param  int $bits
   * @return self
   */
  public static function aes($password, $bits): self {
    return new AESEncryption($password, $bits);
  }

  /**
   * Returns traditional PKZIP cipher
   *
   * @param  string|util.Secret $password
   * @return self
   */
  public static function cipher($password): self {
    return new Cipher($password);
  }
}