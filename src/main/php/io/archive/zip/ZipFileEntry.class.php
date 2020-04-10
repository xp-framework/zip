<?php namespace io\archive\zip;

use util\{Date, Objects};

/**
 * Represents a file entry in a zip archive
 *
 * @test     xp://net.xp_framework.unittest.io.archive.ZipEntryTest
 * @see      xp://io.archive.zip.ZipEntry
 * @purpose  Interface
 */
class ZipFileEntry implements ZipEntry {
  protected 
    $name         = '',
    $size         = 0,
    $mod          = null,
    $compression  = null;
  
  public
    $is   = null,
    $os   = null;
      
  /**
   * Constructor
   *
   * @param   var... $args
   */
  public function __construct() {
    $this->name= '';
    $args= func_get_args();
    foreach ($args as $part) {
      if ($part instanceof ZipDirEntry) {
        $this->name.= $part->getName();
      } else {
        $this->name.= strtr($part, '\\', '/').'/';
      }
    }
    $this->name= rtrim($this->name, '/');
    $this->mod= \util\Date::now();
    $this->compression= [Compression::$NONE, 6];
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
    return $this->compression[0];
  }

  /**
   * Use a given compression
   *
   * @param   int level default 6
   * @param   io.archive.zip.Compression compression
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

  /**
   * Returns whether this entry is a directory
   *
   * @return  bool
   */
  public function isDirectory() {
    return false;
  }

  /**
   * Returns an input stream for reading from this entry
   *
   * @deprecated Use in() instead
   * @return  io.streams.InputStream
   */
  public function getInputStream() {
    return $this->in();
  }

  /**
   * Returns an output stream for writing to this entry
   *
   * @deprecated Use out() instead
   * @return  io.streams.OutputStream
   */
  public function getOutputStream() {
    return $this->out();
  }

  /**
   * Returns an input stream for reading from this entry
   *
   * @return  io.streams.InputStream
   */
  public function in() {
    return $this->compression[0]->getDecompressionStream($this->is);
  }

  /**
   * Returns an output stream for writing to this entry
   *
   * @return  io.streams.OutputStream
   */
  public function out() {
    return $this->os->withCompression($this->compression[0],  $this->compression[1]);
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
      "  [size        ] %d\n".
      "}",
      nameof($this),
      $this->name,
      Objects::stringOf($this->mod),
      Objects::stringOf($this->compression[0]),
      $this->compression[1],
      $this->size
    );
  }
}