<?php namespace io\archive\zip\unittest;

/**
 * Base class for testing zip file contents
 *
 * @see      xp://io.archive.zip.ZipArchiveReader#entries
 */
class ZipFileEntriesTest extends ZipFileContentsTest {

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @return  [:string] content
   */
  protected function entriesWithContentIn(\io\archive\zip\ZipArchiveReader $zip) {
    $entries= [];
    foreach ($zip->entries() as $entry) {
      $entries[$entry->getName()]= $this->entryContent($entry);
    }
    return $entries;
  }
}
