<?php namespace io\archive\zip\unittest;

use io\File;
use io\archive\zip\{ZipArchiveReader, ZipEntry};
use io\streams\{Streams, InputStream};
use test\verify\Runtime;
use util\Secret;

/**
 * Base class for testing zip files
 *
 * @see   net.xp_framework.unittest.io.archive.MalformedZipFileTest
 * @see   net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest
 */
#[Runtime(extensions: ['zlib'])]
abstract class AbstractZipFileTest {

  /** @return iterable */
  protected function passwords() {
    yield ['secret'];
    yield [new Secret('secret')];
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
    return newinstance(InputStream::class, [$resource], [
      'file' => null,
      '__construct' => function($file) { $this->file= $file->open(File::READ); },
      'read'        => function($limit= 8192) { return $this->file->read($limit); },
      'available'   => function() { return $this->file->eof() ? 0 : 1; },
      'close'       => function() { $this->file->close(); },
    ]);
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