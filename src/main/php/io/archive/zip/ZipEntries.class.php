<?php namespace io\archive\zip;

/**
 * Iterates on ZIP archive entries
 *
 * @test    xp://net.xp_framework.unittest.io.archive.ZipFileEntriesTest
 */
class ZipEntries extends \lang\Object implements \Iterator {
  protected $impl= null;
  protected $entry= null;
  protected $offset= 0;
  
  /**
   * Constructor
   *
   * @param   io.archive.zip.AbstractZipReaderImpl impl
   */
  public function __construct($impl) {
    $this->impl= $impl;
    $this->offset= 0;
  }

  /**
   * Returns current value of iteration
   *
   * @return  io.archive.zip.ZipEntry
   */
  public function current() { 
    return $this->entry;
  }

  /**
   * Returns current offset of iteration
   *
   * @return  int
   */
  public function key() { 
    return $this->offset; 
  }

  /**
   * Goes to next
   *
   */
  public function next() { 
    $this->entry= $this->impl->nextEntry();
    $this->offset++;
  }

  /**
   * Rewinds
   *
   */
  public function rewind() { 
    $this->entry= $this->impl->firstEntry();
    $this->offset= 0;
  }
  
  /**
   * Checks whether iteration should continue
   *
   * @return  bool
   */
  public function valid() { 
    return null !== $this->entry; 
  }
}
