<?php namespace io\archive\zip;

use io\streams\{InputStream, Seekable};

/**
 * Read from a zip file
 *
 * Usage in foreach
 * ----------------
 * ```php
 * $z= ZipFile::open(new FileInputStream(new File('dist.zip')));
 * foreach ($reader->entries() as $entry) {
 *   // ...
 * }
 * ```
 *
 * Usage with iterator
 * -------------------
 * ```php
 * $z= ZipFile::open(new FileInputStream(new File('dist.zip')));
 * $it= $z->iterator();
 * while ($it->hasNext()) {
 *   $entry= $it->next();
 *   // ...
 * }
 * ```
 *
 * @test  io.archive.zip.unittest.ZipArchiveReaderTest
 * @test  io.archive.zip.unittest.ZipFileEntriesTest
 * @test  io.archive.zip.unittest.ZipFileIteratorTest
 * @see   io.archive.zip.ZipArchive#open
 */
class ZipArchiveReader {
  protected $impl= NULL;

  /**
   * Creation constructor
   *
   * @param  io.streams.InputStream $stream
   */
  public function __construct(InputStream $stream) {
    if ($stream instanceof Seekable) {
      $this->impl= new RandomAccessZipReaderImpl($stream);
    } else {
      $this->impl= new SequentialZipReaderImpl($stream);
    }
  }

  /**
   * Set password to use when extracting 
   *
   * @param  string $password
   * @return self
   */
  public function usingPassword($password) {
    $this->impl->setPassword($password);
    return $this;
  }

  /**
   * Returns a list of all entries in this zip file
   *
   * @return io.archive.zip.ZipEntries
   */
  public function entries() {
    return new ZipEntries($this->impl);
  }

  /**
   * Returns an iterator of all entries in this zip file
   *
   * @return io.archive.zip.ZipIterator
   */
  public function iterator() {
    return new ZipIterator($this->impl);
  }

  /**
   * Closes underlying stream
   *
   * @return void
   */
  public function close() {
    $this->impl->close();
  }
}