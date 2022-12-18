<?php namespace io\archive\zip;

use io\streams\{MemoryOutputStream, OutputStream};

/**
 * Output stream for files
 *
 * @see   io.archive.zip.ZipArchiveWriter::addFile
 */
class ZipFileOutputStream implements OutputStream {
  protected $writer, $file, $name, $md;
  protected $compression= null;
  protected $data= null;
  protected $size= 0;
  
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
    $this->md= hash_init('crc32b');
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
    hash_update($this->md, $arg);
    $this->compression->write($arg);
  }

  /**
   * Flush this buffer
   *
   * @return void
   */
  public function flush() {
    // NOOP
  }

  /**
   * Close this buffer
   *
   * @return void
   */
  public function close() {
    if (null === $this->data) return;     // Already written

    $crc32= hexdec(hash_final($this->md));
    if ($crc32 > 2147483647) {      // Convert from uint32 to int32
      $crc32= intval($crc32 - 4294967296);
    }
    
    $this->compression->close();
    $bytes= $this->data->bytes();
    $this->writer->writeFile(
      $this->file,
      $this->name,
      $this->size, 
      strlen($bytes),
      $crc32,
      0
    );
    $this->writer->streamWrite($bytes);
    unset($this->data);
  }
}