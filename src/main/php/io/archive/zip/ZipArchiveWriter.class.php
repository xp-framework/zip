<?php namespace io\archive\zip;

use io\streams\OutputStream;
use lang\{Closeable, IllegalArgumentException};
use util\{Date, Secret};

/**
 * Writes to a ZIP archive
 *
 * @see   io.archive.zip.ZipArchive#create
 * @test  io.archive.zip.unittest.ZipArchiveWriterTest
 */
class ZipArchiveWriter implements Closeable {
  public $stream;

  protected
    $dir        = [],
    $pointer    = 0,
    $out        = null,
    $unicode    = false,
    $encryption = null;

  const EOCD = "\x50\x4b\x05\x06\x00\x00\x00\x00";
  const FHDR = "\x50\x4b\x03\x04";
  const DHDR = "\x50\x4b\x01\x02";

  /**
   * Creation constructor
   *
   * @param  io.streams.OutputStream $stream
   * @param  bool $unicode whether to use unicode for entry names
   */
  public function __construct(OutputStream $stream, $unicode= false) {
    $this->stream= $stream;
    $this->unicode= $unicode;
  }
  
  /**
   * Set whether to use unicode for entry names. Note this is not supported 
   * by for example Windows explorer or the "unzip" command line utility,
   * although the Language Encoding (EFS) bit is set - 7-zip, on the other
   * side, as also this implementation, will handle the name correctly. 
   * Java's jar utility will even expect utf-8, and choke on any other names!
   *
   * @param   bool unicode true to use unicode, false otherwise
   * @return  io.archive.zip.ZipArchiveWriter
   */
  public function usingUnicodeNames($unicode= true) {
    $this->unicode= $unicode;
    return $this;
  }

  /**
   * Set encryption to use when adding entries
   *
   * @param  ?io.zip.archive.Encryption|util.Secret|string $encryption
   * @return self
   */
  public function encryptWith($encryption) {
    if (null === $encryption) {
      $this->encryption= null;
    } else if ($encryption instanceof Encryption) {
      $this->encryption= $encryption;
    } else {
      $this->encryption= Encryption::cipher($encryption);
    }
    return $this;
  }

  /**
   * Set password to use when adding entries
   *
   * @deprecated Use encryptWith() instead
   * @param  ?string|util.Secret $password
   * @return self
   */
  public function usingPassword($password) {
    return $this->encryptWith($password);
  }

  /**
   * Adds a directory entry
   *
   * @param   io.archive.zip.ZipDirEntry entry
   * @return  io.archive.zip.ZipDirEntry the added directory
   * @throws  lang.IllegalArgumentException in case the filename is longer than 65535 bytes
   */
  public function addDir(ZipDirEntry $entry) {
    $name= iconv(\xp::ENCODING, $this->unicode ? 'utf-8' : 'cp437', str_replace('\\', '/', $entry->getName()));
    $nameLength= strlen($name);
    if ($nameLength > 0xFFFF) {
      throw new IllegalArgumentException('Filename too long ('.$nameLength.')');
    }

    // Ensure any open stream is closed
    $this->out && $this->out->close();
    $this->out= null;
    
    $mod= $entry->getLastModified();
    $info= pack(
      'vvvvvVVVvv',
      10,                       // version
      $this->unicode ? 2048 : 0,// flags
      0,                        // compression method
      $this->dosTime($mod),     // last modified dostime
      $this->dosDate($mod),     // last modified dosdate
      0,                        // CRC32 checksum
      0,                        // compressed size
      0,                        // uncompressed size
      $nameLength,              // filename length
      0                         // extra field length
    );
    $this->stream->write(self::FHDR.$info.$name);
    
    $this->dir[$name]= ['info' => $info, 'pointer' => $this->pointer, 'type' => 0x10];
    $this->pointer+= strlen(self::FHDR) + strlen($info) + $nameLength;

    return $entry;
  }

  /**
   * Adds a file entry
   *
   * @param   io.archive.zip.ZipFileEntry entry
   * @return  io.archive.zip.ZipFileEntry entry
   * @throws  lang.IllegalArgumentException in case the filename is longer than 65535 bytes
   */
  public function addFile(ZipFileEntry $entry) {
    $name= iconv(\xp::ENCODING, $this->unicode ? 'utf-8' : 'cp437', str_replace('\\', '/', $entry->getName()));
    $nameLength= strlen($name);
    if ($nameLength > 0xFFFF) {
      throw new IllegalArgumentException('Filename too long ('.$nameLength.')');
    }

    // Ensure any open stream is closed
    $this->out && $this->out->close();
    $this->out= $entry->os= new ZipFileOutputStream($this, $entry, $name);
    return $entry->useEncryption($this->encryption, false);
  }

  /**
   * Adds an entry
   *
   * @param   io.archive.zip.ZipEntry entry
   * @return  io.archive.zip.ZipEntry entry
   * @throws  lang.IllegalArgumentException in case the filename is longer than 65535 bytes
   */
  public function add(ZipEntry $entry) {
    if ($entry->isDirectory()) {
      return $this->addDir($entry);
    } else {
      return $this->addFile($entry);
    }
  }
  
  /**
   * Returns a time in the format used by MS-DOS.
   *
   * @see     http://www.vsft.com/hal/dostime.htm
   * @param   util.Date date
   * @return  int
   */
  protected function dosTime(Date $date) {
    return 
      (((($date->getHours() & 0x1F) << 6) | ($date->getMinutes() & 0x3F)) << 5) | 
      ((int)($date->getSeconds() / 2) & 0x1F)
    ;
  }

  /**
   * Returns a date in the format used by MS-DOS.
   *
   * @see     http://www.vsft.com/hal/dostime.htm
   * @param   util.Date date
   * @return  int
   */
  protected function dosDate(Date $date) {
    return
      ((((($date->getYear() - 1980) & 0x7F) << 4) | ($date->getMonth() & 0x0F)) << 5) |
      ($date->getDay() & 0x1F)
    ;
  }

  /**
   * Write a file entry
   *
   * @param  int $flags
   * @param  int $compressiom
   * @param  int $modified
   * @param  int $crc32
   * @param  int $compressed
   * @param  int $size
   * @param  string $name
   * @param  string $extra
   * @return void
   */
  public function addEntry($flags, $compression, $modified, $crc32, $compressed, $size, $name, $extra) {
    $nameLength= strlen($name);
    $extraLength= strlen($extra);

    $info= pack(
      'vvvvvVVVvv',
      10,                        // version
      $this->unicode ? 2048 | $flags : $flags,
      $compression,              // compression algorithm
      $this->dosTime($modified), // last modified dostime
      $this->dosDate($modified), // last modified dosdate
      $crc32,                    // CRC32 checksum
      $compressed,               // compressed size
      $size,                     // uncompressed size
      $nameLength,               // filename length
      $extraLength               // extra field length
    );

    $this->stream->write(self::FHDR.$info);
    $nameLength && $this->stream->write($name);
    $extraLength && $this->stream->write($extra);

    $this->dir[$name]= ['info' => $info, 'pointer' => $this->pointer, 'type' => 0x20];
    $this->pointer+= strlen(self::FHDR) + strlen($info) + $nameLength + $extraLength + $compressed;
  }

  /**
   * Closes this zip archive
   *
   * @return void
   */
  public function close() {
    if (null === $this->dir) return;

    // Close any open streams
    $this->out && $this->out->close();

    // Build central directory
    $comment= '';
    $l= 0;
    foreach ($this->dir as $name => $entry) {
      $s= (
        self::DHDR.
        "\x14\x0b".           // version made by
        $entry['info'].       // see addEntry()
        "\x00\x00".           // file comment length
        "\x00\x00".           // disk number start
        "\x01\x00".           // internal file attributes
        pack('V', $entry['type']).
        pack('V', $entry['pointer']).
        $name
      );
      $l+= strlen($s);
      $this->stream->write($s);
    }

    // End of central directory
    $this->stream->write(self::EOCD);
    $this->stream->write(pack(
      'vvVVv', 
      sizeof($this->dir),     // total #entries in central dir on this disk
      sizeof($this->dir),     // total #entries in central dir
      $l,                     // size of central dir
      $this->pointer,         // offset of start of central directory with respect to the starting disk number
      strlen($comment)
    ));
    $this->stream->write($comment);
    $this->stream->close();
    $this->dir= null;
  }
  
  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    $s= nameof($this).'('.$this->stream->toString().")@{\n";
    foreach ($this->dir as $name => $entry) {
      $s.= '  dir{'.dechex($entry['type']).': "'.$name.'" @ '.$entry['pointer']."}\n";
    }
    return $s.'}';
  }
}