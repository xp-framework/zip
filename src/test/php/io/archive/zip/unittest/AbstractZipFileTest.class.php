<?php namespace io\archive\zip\unittest;

use io\File;
use io\archive\zip\{ZipArchiveReader, ZipEntry, Compression, Encryption};
use io\streams\{Streams, InputStream};
use util\Secret;

abstract class AbstractZipFileTest {
  const SECRET= 'secret';

  /** @return iterable */
  protected function encryption() {
    yield [self::SECRET];
    yield [new Secret(self::SECRET)];
    yield [Encryption::cipher(new Secret(self::SECRET))];
    yield [Encryption::aes(new Secret(self::SECRET), 128)];
    yield [Encryption::aes(new Secret(self::SECRET), 192)];
    yield [Encryption::aes(new Secret(self::SECRET), 256)];
  }

  /** @return iterable */
  protected function compression() {
    yield [Compression::$NONE];
    extension_loaded('zlib') && yield [Compression::$GZ];
    extension_loaded('bz2') && yield [Compression::$BZ];
  }

  /**
   * Returns a random access input stream for a given zip file
   *
   * @param   string $package
   * @param   string $name
   * @return  io.streams.InputStream
   */
  protected function randomAccess($package, $name) {
    return typeof($this)
      ->getPackage()
      ->getPackage($package)
      ->getResourceAsStream($name.'.zip')
      ->in()
    ;
  }

  /**
   * Returns a sequential access input stream for a given zip file
   *
   * @param   string $package
   * @param   string $name
   * @return  io.streams.InputStream
   */
  protected function sequentialAccess($package, $name) {
    $resource= typeof($this)->getPackage()->getPackage($package)->getResourceAsStream($name.'.zip');
    return new class($resource) implements InputStream {
      protected $file;

      public function __construct($file) {
        $this->file= $file->open(File::READ);
      }

      public function read($limit= 8192) {
        return $this->file->read($limit);
      }

      public function available() {
        return $this->file->eof() ? 0 : 1;
      }

      public function close() {
        $this->file->close();
      }
    };
  }

  /**
   * Returns entry content; or NULL for directories
   *
   * @param   io.archive.zip.ZipEntry $entry
   * @return  string
   */
  protected function entryContent(ZipEntry $entry) {
    if ($entry->isDirectory()) {
      return null;
    } else {
      return (string)Streams::readAll($entry->in());
    }
  }

  /**
   * Returns an archive reader for a given zip file
   *
   * @param   string $package
   * @param   string $name
   * @return  io.archive.zip.ZipArchiveReader
   */
  protected function archiveReaderFor($package, $name) {
    return new ZipArchiveReader($this->randomAccess($package, $name));
  }
  
  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.archive.zip.ZipArchiveReader $reader
   * @return  io.archive.zip.ZipEntry[]
   */
  protected function entriesIn(ZipArchiveReader $reader) {
    return iterator_to_array($reader->entries());
  }
}