<?php namespace io\archive\zip;

use Iterator, ReturnTypeWillChange;

/**
 * Iterates on ZIP archive entries
 *
 * @test  io.archive.zip.unittest.ZipFileEntriesTest
 */
class ZipEntries implements Iterator {
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
  #[ReturnTypeWillChange]
  public function current() { 
    return $this->entry;
  }

  /**
   * Returns current offset of iteration
   *
   * @return  int
   */
  #[ReturnTypeWillChange]
  public function key() { 
    return $this->offset; 
  }

  /**
   * Goes to next
   *
   */
  #[ReturnTypeWillChange]
  public function next() { 
    $this->entry= $this->impl->nextEntry();
    $this->offset++;
  }

  /**
   * Rewinds
   *
   */
  #[ReturnTypeWillChange]
  public function rewind() { 
    $this->entry= $this->impl->firstEntry();
    $this->offset= 0;
  }
  
  /**
   * Checks whether iteration should continue
   *
   * @return  bool
   */
  #[ReturnTypeWillChange]
  public function valid() { 
    return null !== $this->entry; 
  }
}