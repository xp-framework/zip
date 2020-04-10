<?php namespace io\archive\zip;

/**
 * Read from a zip file
 *
 * Usage in foreach
 * ----------------
 * <code>
 *   $z= ZipFile::open(new FileInputStream(new File('dist.zip')));
 *   foreach ($reader->entries() as $entry) {
 *     // ...
 *   }
 * </code>
 *
 * Usage with iterator
 * -------------------
 * <code>
 *   $z= ZipFile::open(new FileInputStream(new File('dist.zip')));
 *   $it= $z->iterator();
 *   while ($it->hasNext()) {
 *     $entry= $it->next();
 *     // ...
 *   }
 * </code>
 *
 * @test    xp://net.xp_framework.unittest.io.archive.ZipArchiveReaderTest
 * @test    xp://net.xp_framework.unittest.io.archive.ZipFileEntriesTest
 * @test    xp://net.xp_framework.unittest.io.archive.ZipFileIteratorTest
 * @see     xp://io.archive.zip.ZipArchive#open
 */
class ZipArchiveReader {
  protected $impl= NULL;

  /**
   * Creation constructor
   *
   * @param   io.streams.InputStream stream
   */
  public function __construct(\io\streams\InputStream $stream) {
    if ($stream instanceof \io\streams\Seekable) {
      $this->impl= new RandomAccessZipReaderImpl($stream);
    } else {
      $this->impl= new SequentialZipReaderImpl($stream);
    }
  }

  /**
   * Set password to use when extracting 
   *
   * @param   string password
   * @return  io.archive.zip.ZipArchiveReader this
   */
  public function usingPassword($password) {
    $this->impl->setPassword($password);
    return $this;
  }

  /**
   * Returns a list of all entries in this zip file
   *
   * @return  io.archive.zip.ZipEntries
   */
  public function entries() {
    return new ZipEntries($this->impl);
  }

  /**
   * Returns an iterator of all entries in this zip file
   *
   * @return  io.archive.zip.ZipIterator
   */
  public function iterator() {
    return new ZipIterator($this->impl);
  }

  /**
   * Closes underlying stream
   */
  public function close() {
    $this->impl->close();
  }
}