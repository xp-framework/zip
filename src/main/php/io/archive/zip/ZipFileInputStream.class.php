<?php namespace io\archive\zip;

use io\streams\{Seekable, InputStream};

/**
 * Zip File input stream. Reads from the current position up until a
 * certain length.
 */
class ZipFileInputStream implements InputStream, Seekable {
  protected $reader, $start, $length;
  protected $pos= 0;

  /**
   * Constructor
   *
   * @param  io.archive.zip.AbstractZipReaderImpl $reader
   * @param  int $start
   * @param  int $length
   */
  public function __construct(AbstractZipReaderImpl $reader, $start, $length) {
    $this->reader= $reader;
    $this->start= $start;
    $this->length= $length;
  }

  /** @return int */
  public function tell() { return $this->pos; }

  /**
   * Seek
   *
   * @param  int $offset
   * @param  int $whence
   * @return void
   */
  public function seek($offset, $whence= SEEK_SET) {
    switch ($whence) {
      case SEEK_SET: $this->pos= $offset; break;
      case SEEK_END: $this->pos= $length + $offset; break;
      case SEEK_CUR: $this->pos+= $offset; break;
    }
    $this->reader->streamPosition($this->start + $this->pos);
  }

  /**
   * Read a string
   *
   * @param  int $limit default 8192
   * @return string
   */
  public function read($limit= 8192) {
    $chunk= $this->reader->streamRead(min($limit, $this->length - $this->pos));
    $l= strlen($chunk);
    $this->pos+= $l;
    $this->reader->skip-= $l;
    return $chunk;
  }

  /**
   * Returns the number of bytes that can be read from this stream 
   * without blocking.
   *
   * @return int
   */
  public function available() {
    return $this->pos < $this->length ? $this->reader->streamAvailable() : 0;
  }

  /**
   * Close this buffer
   *
   * @return void
   */
  public function close() {
    // NOOP, leave underlying stream open
  }
}