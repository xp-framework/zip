<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipFile;
use io\archive\zip\ZipEntry;
use io\archive\zip\ZipArchiveReader;
use io\streams\Streams;

/**
 * Base class for testing zip files
 *
 * @see   xp://net.xp_framework.unittest.io.archive.MalformedZipFileTest
 * @see   xp://net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest
 */
#[@action(new \unittest\actions\ExtensionAvailable('zlib'))]
abstract class ZipFileTest extends \unittest\TestCase {

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
      return (string)Streams::readAll($entry->getInputStream());
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
    return ZipFile::open($this->getClass()
      ->getPackage()
      ->getPackage($package)
      ->getResourceAsStream($name.'.zip')
      ->getInputStream()
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
