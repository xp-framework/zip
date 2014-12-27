<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipFile;
use io\archive\zip\ZipFileEntry;
use io\archive\zip\ZipDirEntry;
use io\streams\MemoryOutputStream;
use io\streams\MemoryInputStream;
use io\streams\Streams;

class ZipArchiveWriterTest extends ZipFileTest {

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.streams.MemoryOutputStream $out
   * @return  [:string] content
   */
  protected function entriesWithContentIn($out) {
    $zip= ZipFile::open(new MemoryInputStream($out->getBytes()));

    $entries= [];
    foreach ($zip->entries() as $entry) {
      if ($entry->isDirectory()) {
        $entries[$entry->getName()]= null;
      } else {
        $entries[$entry->getName()]= Streams::readAll($entry->getInputStream());
      }
    }
    return $entries;
  }

  #[@test]
  public function zipfile_create() {
    $this->assertInstanceOf('io.archive.zip.ZipArchiveWriter', ZipFile::create(new MemoryOutputStream()));
  }

  #[@test]
  public function adding_a_file() {
    $out= new MemoryOutputStream();
    
    $fixture= ZipFile::create($out);
    $fixture->addFile(new ZipFileEntry('test.txt'))->getOutputStream()->write('File contents');
    $fixture->close();

    $this->assertEquals(['test.txt' => 'File contents'], $this->entriesWithContentIn($out));
  }

  #[@test]
  public function adding_a_dir() {
    $out= new MemoryOutputStream();
    
    $fixture= ZipFile::create($out);
    $fixture->addDir(new ZipDirEntry('test'));
    $fixture->close();

    $this->assertEquals(['test/' => null], $this->entriesWithContentIn($out));
  }
}