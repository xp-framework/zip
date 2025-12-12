<?php namespace io\archive\zip;

use io\streams\OutputStream;

/**
 * Output stream for files
 *
 * @see  io.archive.zip.ZipArchiveWriter::addFile
 */
class ZipFileOutputStream implements OutputStream {
  protected $writer, $file, $name, $md, $size;

  public $stream, $data;
  
  /**
   * Constructor
   *
   * @param  io.archive.zip.ZipArchiveWriter $writer
   * @param  io.archive.zip.ZipFileEntry $file
   * @param  string $name
   */
  public function __construct(ZipArchiveWriter $writer, ZipFileEntry $file, $name) {
    $this->writer= $writer;
    $this->file= $file;
    $this->name= $name;
    $this->size= 0;
    $this->md= hash_init('crc32b');
    $this->stream= $this->data= new Buffer();
  }
  
  /**
   * Write a string
   *
   * @param   var arg
   */
  public function write($arg) {
    $this->size+= strlen($arg);
    hash_update($this->md, $arg);
    $this->stream->write($arg);
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
    if (null === $this->data) return; // Already written

    $crc32= hexdec(hash_final($this->md));
    if ($crc32 > 2147483647) {        // Convert from uint32 to int32
      $crc32= intval($crc32 - 4294967296);
    }

    $this->stream->close();
    $compression= $this->file->compression();

    // Encrypt the (compressed) data buffer
    if ($encryption= $this->file->encryption()) {
      $encryption->header(
        $this->writer,
        $compression ? $compression[0]->ordinal() : 0,
        $this->file->getLastModified(),
        $crc32,
        $this->data->length,
        $this->size,
        $this->name
      );
      $out= $encryption->out($this->writer, $crc32);
    } else {
      $this->writer->addEntry(
        0,
        $compression ? $compression[0]->ordinal() : 0,
        $this->file->getLastModified(),
        $crc32,
        $this->data->length,
        $this->size,
        $this->name,
        ''
      );
      $out= $this->writer->stream;
    }

    while ($this->data->available()) {
      $out->write($this->data->read());
    }
    $out->close();
    $this->data= null;
  }
}