<?php namespace io\archive\zip\unittest;

use io\archive\zip\{ZipArchiveReader, ZipEntry, ZipFile};
use io\streams\Streams;
use unittest\TestCase;
use unittest\actions\ExtensionAvailable;

/**
 * Base class for testing zip files
 *
 * @see   xp://net.xp_framework.unittest.io.archive.MalformedZipFileTest
 * @see   xp://net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest
 */
#[Action(eval: 'new ExtensionAvailable("zlib")')]
abstract class AbstractZipFileTest extends TestCase {

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
    return ZipFile::open(typeof($this)
      ->getPackage()
      ->getPackage($package)
      ->getResourceAsStream($name.'.zip')
      ->in()
    );
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