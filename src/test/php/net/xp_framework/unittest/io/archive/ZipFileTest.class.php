<?php namespace net\xp_framework\unittest\io\archive;

use unittest\TestCase;
use io\archive\zip\ZipFile;
use lang\Runtime;


/**
 * Base class for testing zip files
 *
 * @see   xp://net.xp_framework.unittest.io.archive.MalformedZipFileTest
 * @see   xp://net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest
 */
abstract class ZipFileTest extends TestCase {

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    if (!Runtime::getInstance()->extensionAvailable('zlib')) {
      throw new \unittest\PrerequisitesNotMetError('ZLib support not available', null, array('ext/zlib'));
    }
  }

  /**
   * Returns an archive reader for a given zip file
   *
   * @param   string package
   * @param   string name
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
   * @param   io.archive.zip.ZipArchiveReader reader
   * @return  io.archive.zip.ZipEntry[]
   */
  protected function entriesIn(\io\archive\zip\ZipArchiveReader $zip) {
    $entries= array();
    foreach ($zip->entries() as $entry) {
      $entries[]= $entry;
    }
    return $entries;
  }
}
