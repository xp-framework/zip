<?php namespace io\archive\zip;

use util\{Date, Objects};

/**
 * Represents a Dir entry in a zip archive
 *
 * @test  net.xp_framework.unittest.io.archive.ZipEntryTest
 * @see   io.archive.zip.ZipEntry
 */
class ZipDirEntry implements ZipEntry {
  protected 
    $name        = '', 
    $mod         = null,
    $compression = null;
      
  /**
   * Constructor
   *
   * @param   var... $args
   */
  public function __construct(... $args) {
    $this->name= '';
    foreach ($args as $part) {
      if ($part instanceof self) {
        $this->name.= $part->getName();
      } else {
        $this->name.= strtr($part, '\\', '/').'/';
      }
    }
    $this->name= rtrim($this->name, '/').'/';
    $this->mod= Date::now();
    $this->compression= Compression::$NONE;
  }
  
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
   * @return  io.archive.zip.Compression
   */
  public function getCompression() {
    return $this->compression;
  }
  
  /**
   * Use a given compression
   *
   * @param   io.archive.zip.Compression compression
   */
  public function setCompression(Compression $compression) {
    $this->compression= $compression;
  }

  /**
   * Gets a zip entry's size
   *
   * @return  int
   */
  public function getSize() {
    return 0;
  }

  /**
   * Sets a zip entry's size
   *
   * @param   int size
   */
  public function setSize($size) {
    // NOOP
  }

  /**
   * Returns whether this entry is a directory
   *
   * @return  bool
   */
  public function isDirectory() {
    return true;
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
      "}",
      nameof($this),
      $this->name,
      Objects::stringOf($this->mod)
    );
  }
}