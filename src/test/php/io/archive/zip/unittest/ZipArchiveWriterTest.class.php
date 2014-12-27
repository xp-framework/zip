<?php namespace io\archive\zip\unittest;

use io\archive\zip\ZipFile;
use io\archive\zip\ZipFileEntry;
use io\archive\zip\ZipDirEntry;
use io\streams\MemoryOutputStream;
use io\streams\MemoryInputStream;

class ZipArchiveWriterTest extends ZipFileTest {

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.streams.MemoryOutputStream $out
   * @param   string $password
   * @return  [:string] content
   */
  protected function entriesWithContentIn($out, $password= null) {
    $zip= ZipFile::open(new MemoryInputStream($out->getBytes()))->usingPassword($password);

    $entries= [];
    foreach ($zip->entries() as $entry) {
      $entries[$entry->getName()]= $this->entryContent($entry);
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

  #[@test]
  public function adding_files_and_dir() {
    $out= new MemoryOutputStream();

    $fixture= ZipFile::create($out);
    $dir= $fixture->addDir(new ZipDirEntry('test/'));
    $fixture->addFile(new ZipFileEntry($dir, '1.txt'))->getOutputStream()->write('File #1');
    $fixture->addFile(new ZipFileEntry($dir, '2.txt'))->getOutputStream()->write('File #2');
    $fixture->close();

    $this->assertEquals(
      ['test/' => null, 'test/1.txt' => 'File #1', 'test/2.txt' => 'File #2'],
      $this->entriesWithContentIn($out)
    );
  }

  #[@test]
  public function using_password_protection() {
    $out= new MemoryOutputStream();

    $fixture= ZipFile::create($out)->usingPassword('secret');
    $fixture->addFile(new ZipFileEntry('test.txt'))->getOutputStream()->write('File contents');
    $fixture->close();

    $this->assertEquals(['test.txt' => 'File contents'], $this->entriesWithContentIn($out, 'secret'));
  }

  #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Filename too long (65536)')]
  public function cannot_add_files_with_names_longer_than_65535_characters() {
    $fixture= ZipFile::create(new MemoryOutputStream());
    $fixture->addFile(new ZipFileEntry(str_repeat('n', 65535 + 1)));
  }

  #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Filename too long (65536)')]
  public function cannot_add_dirs_with_names_longer_than_65535_characters() {
    $fixture= ZipFile::create(new MemoryOutputStream());
    $fixture->addDir(new ZipDirEntry(str_repeat('n', 65535).'/'));
  }
}