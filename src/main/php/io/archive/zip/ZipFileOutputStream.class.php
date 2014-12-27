<?php namespace io\archive\zip;

use io\streams\OutputStream;
use io\streams\MemoryOutputStream;
use security\checksum\CRC32;


/**
 * Output stream for files
 *
 * @see      xp://io.archive.zip.ZipArchiveWriter#addFile
 * @purpose  Stream
 */
class ZipFileOutputStream extends \lang\Object implements OutputStream {
  protected
    $writer      = null,
    $compression = null,
    $data        = null,
    $name        = null,
    $size        = 0,
    $md          = null;
  
  /**
   * Constructor
   *
   * @param   io.archive.zip.ZipArchiveWriter $writer
   * @param   io.archive.zip.ZipFileEntry $file
   * @param   string $name
   */
  public function __construct(ZipArchiveWriter $writer, ZipFileEntry $file, $name) {
    $this->writer= $writer;
    $this->file= $file;
    $this->name= $name;
    $this->data= null;
    $this->md= CRC32::digest();
  }
  
  /**
   * Sets compression method
   *
   * @param   io.archive.zip.Compression compression
   * @param   int level default 6
   * @return  io.archive.zip.ZipFileOutputStream this
   */
  public function withCompression(Compression $compression, $level= 6) {
    $this->data= new MemoryOutputStream();
    $this->compression= $compression->getCompressionStream($this->data, $level);
    return $this;
  }
  
  /**
   * Write a string
   *
   * @param   var arg
   */
  public function write($arg) {
    $this->size+= strlen($arg);
    $this->md->update($arg);
    $this->compression->write($arg);
  }

  /**
   * Flush this buffer
   *
   */
  public function flush() {
    // NOOP
  }

  /**
   * Close this buffer
   *
   */
  public function close() {
    if (null === $this->data) return;     // Already written
    
    $this->compression->close();
    $bytes= $this->data->getBytes();
    $this->writer->writeFile(
      $this->file,
      $this->name,
      $this->size, 
      strlen($bytes),
      create(new CRC32($this->md->digest()))->asInt32(),
      0
    );
    $this->writer->streamWrite($bytes);
    delete($this->data);
  }
}
