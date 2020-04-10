<?php namespace io\archive\zip;

use io\streams\{MemoryOutputStream, OutputStream};

/**
 * Output stream for files
 *
 * @see   xp://io.archive.zip.ZipArchiveWriter#addFile
 */
class CipheringZipFileOutputStream implements OutputStream {
  protected
    $writer      = null,
    $compression = null,
    $data        = null,
    $name        = null,
    $size        = 0,
    $md          = null;
    
  protected $cipher= null;
  
  /**
   * Constructor
   *
   * @param   io.archive.zip.ZipArchiveWriter $writer
   * @param   io.archive.zip.ZipFileEntry $file
   * @param   string $name
   * @param   io.archive.zip.ZipCipher $cipher
   */
  public function __construct(ZipArchiveWriter $writer, ZipFileEntry $file, $name, ZipCipher $cipher) {
    $this->writer= $writer;
    $this->file= $file;
    $this->name= $name;
    $this->data= null;
    $this->md= hash_init('crc32b');
    $this->cipher= $cipher;
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
    $this->compression= [$compression, $level];
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
    $this->data->write($arg);
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

    // Calculate CRC32
    $crc32= hexdec(hash_final($this->md));
    if ($crc32 > 2147483647) {      // Convert from uint32 to int32
      $crc32= intval($crc32 - 4294967296);
    }

    // Create random bytes
    $rand= '';
    for ($i= 0; $i < 11; $i++) {
      $rand.= chr(mt_rand(0, 255));
    }
    $preamble= $this->cipher->cipher($rand.chr(($crc32 >> 24) & 0xFF));
    
    // Now cipher and the compress raw bytes
    $compressed= new MemoryOutputStream();
    $compression= $this->compression[0]->getCompressionStream($compressed, $this->compression[1]);
    $compression->write($this->cipher->cipher($this->data->getBytes()));
    $bytes= $compressed->getBytes();
    
    // Finally, write header, preamble and bytes
    $this->writer->writeFile(
      $this->file,
      $this->name,
      $this->size, 
      strlen($bytes) + strlen($preamble),
      $crc32,
      1
    );
    $this->writer->streamWrite($preamble);
    $this->writer->streamWrite($bytes);
    unset($this->data);
  }
}