<?php namespace io\archive\zip;

use util\{Date, Objects};

/**
 * Represents a file entry in a zip archive
 *
 * @test  io.archive.zip.unittest.ZipEntryTest
 * @see   io.archive.zip.ZipEntry
 */
class ZipFileEntry implements ZipEntry {
  protected 
    $name         = '',
    $size         = 0,
    $crc32        = 0,
    $mod          = null;

  protected
    $compression  = null,
    $encryption   = null;
  
  public
    $is   = null,
    $os   = null;
      
  /**
   * Constructor
   *
   * @param   var... $args
   */
  public function __construct(... $args) {
    $this->name= '';
    foreach ($args as $part) {
      if ($part instanceof ZipDirEntry) {
        $this->name.= $part->getName();
      } else {
        $this->name.= strtr($part, '\\', '/').'/';
      }
    }
    $this->name= rtrim($this->name, '/');
    $this->mod= Date::now();
  }

  /**
   * Returns whether this entry is a directory
   *
   * @return  bool
   */
  public function isDirectory() { return false; }

  /**
   * Gets a zip entry's name
   *
   * @return  string
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * Gets a zip entry's last modification time
   *
   * @return  util.Date
   */
  public function getLastModified() {
    return $this->mod;
  }

  /**
   * Sets a zip entry's last modification time
   *
   * @param   util.Date lastModified
   */
  public function setLastModified(Date $lastModified) {
    $this->mod= $lastModified;
  }

  /**
   * Returns which compression was used
   *
   * @return io.archive.zip.Compression
   */
  public function getCompression() {
    return $this->compression[0];
  }

  /**
   * Use a given compression
   *
   * @param  io.archive.zip.Compression $compression
   * @param  int $level default 6
   */
  public function setCompression(Compression $compression, $level= 6) {
    $this->compression= [$compression, $level];
  }

  /**
   * Gets a zip entry's size
   *
   * @return  int
   */
  public function getSize() {
    return $this->size;
  }

  /**
   * Sets a zip entry's size
   *
   * @param   int size
   */
  public function setSize($size) {
    $this->size= $size;
  }

  /** @return int */
  public function crc32() { return $this->crc32; }

  /**
   * Sets CRC32 checksum
   *
   * @param  int
   * @return self
   */
  public function withCrc32($crc32) {
    $this->crc32= $crc32;
    return $this;
  }

  /** @return io.archive.zip.Compression */
  public function compression() { return $this->compression; }

  /**
   * Use a given compression
   *
   * @param  io.archive.zip.Compression $compression
   * @param  int $level default 6
   * @return self
   */
  public function useCompression(Compression $compression, $level= 6) {
    $this->compression= [$compression, $level];
    return $this;
  }

  /** @return io.archive.zip.Encryption */
  public function encryption() { return $this->encryption; }

  /**
   * Use a given encryption
   *
   * @param  ?io.archive.zip.Encryption $encryption
   * @param  bool $overwrite
   * @return self
   */
  public function useEncryption($encryption, $overwrite= true) {
    if (null === $this->encryption || $overwrite) {
      $this->encryption= $encryption;
    }
    return $this;
  }

  /**
   * Returns an input stream for reading from this entry
   *
   * @return  io.streams.InputStream
   */
  public function in() {
    $is= $this->is;
    $is->seek(0);

    if ($this->encryption) {
      $is= $this->encryption->in($is, $this->crc32);
    }

    if ($this->compression) {
      $is= $this->compression[0]->getDecompressionStream($is);
    }

    return $is;
  }

  /**
   * Returns an output stream for writing to this entry
   *
   * @return  io.streams.OutputStream
   */
  public function out() {
    $os= $this->os;

    if ($this->compression) {
      $os->stream= $this->compression[0]->getCompressionStream($os->stream, $this->compression[1]);
    }

    return $os;
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return sprintf(
      "%s(%s)@{\n".
      "  [lastModified] %s\n".
      "  [compression ] %s level %d\n".
      "  [encryption  ] %s\n".
      "  [size        ] %d\n".
      "  [crc32       ] %d\n".
      "}",
      nameof($this),
      $this->name,
      Objects::stringOf($this->mod),
      $this->compression ? Objects::stringOf($this->compression[0]) : 'NONE',
      $this->compression ? $this->compression[1] : 0,
      Objects::stringOf($this->encryption),
      $this->size,
      $this->crc32
    );
  }
}