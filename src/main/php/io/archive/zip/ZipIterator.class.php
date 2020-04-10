<?php namespace io\archive\zip;

use util\NoSuchElementException;

/**
 * Iterates on ZIP archive entries
 *
 * @test    xp://net.xp_framework.unittest.io.archive.ZipFileIteratorTest
 */
class ZipIterator implements \util\XPIterator {
  protected $impl= null;
  protected $entry= null;
  protected $more= true;
  
  /**
   * Constructor
   *
   * @param   io.archive.zip.AbstractZipReaderImpl impl
   */
  public function __construct($impl) {
    $this->impl= $impl;
    $this->entry= $this->impl->firstEntry();
    $this->more= null !== $this->entry;
  }
  
  /**
   * Returns whether there are more entries, forwarding to the next
   * one if necessary.
   *
   * @return  bool
   */
  protected function nextEntry() {
    if ($this->more && null === $this->entry) {
      if (null === ($this->entry= $this->impl->nextEntry())) {
        $this->more= false;
      }
    }
    return $this->more;
  }

  /**
   * Returns whether there are more entries in the zip file
   *
   * @return  bool
   */
  public function hasNext() {
    return $this->nextEntry();
  }
  
  /**
   * Returns the next entry in the zip file
   *
   * @return  io.archive.zip.ZipEntry
   * @throws  util.NoSuchElementException when there are no more elements
   */
  public function next() {
    if (!$this->nextEntry()) {
      throw new NoSuchElementException('No more entries in ZIP file');
    }

    $entry= $this->entry;
    $this->entry= null;
    return $entry;
  }
}