<?php namespace io\archive\zip\unittest;

use io\archive\zip\{ZipArchiveWriter, ZipDirEntry, ZipFile, ZipFileEntry};
use io\streams\{MemoryInputStream, MemoryOutputStream};
use lang\IllegalArgumentException;
use unittest\{Expect, Test};

class ZipArchiveWriterTest extends AbstractZipFileTest {
  protected $out, $fixture;

  /**
   * Creates fixture and output stream.
   *
   * @return void
   */
  public function setUp() {
    $this->out= new MemoryOutputStream();
    $this->fixture= new ZipArchiveWriter($this->out);
  }

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.streams.MemoryOutputStream $this->out
   * @param   string $password
   * @return  [:string] content
   */
  protected function entriesWithContentIn($out, $password= null) {
    $zip= ZipFile::open(new MemoryInputStream($out->bytes()))->usingPassword($password);

    $entries= [];
    foreach ($zip->entries() as $entry) {
      $entries[$entry->getName()]= $this->entryContent($entry);
    }
    return $entries;
  }

  #[Test]
  public function adding_a_file_via_addFile() {
    $this->fixture->addFile(new ZipFileEntry('test.txt'))->out()->write('File contents');
    $this->fixture->close();

    $this->assertEquals(['test.txt' => 'File contents'], $this->entriesWithContentIn($this->out));
  }

  #[Test]
  public function adding_a_file_via_add() {
    $this->fixture->add(new ZipFileEntry('test.txt'))->out()->write('File contents');
    $this->fixture->close();

    $this->assertEquals(['test.txt' => 'File contents'], $this->entriesWithContentIn($this->out));
  }

  #[Test]
  public function adding_a_dir_via_addDir() {
    $this->fixture->addDir(new ZipDirEntry('test'));
    $this->fixture->close();

    $this->assertEquals(['test/' => null], $this->entriesWithContentIn($this->out));
  }

  #[Test]
  public function adding_a_dir_via_add() {
    $this->fixture->add(new ZipDirEntry('test'));
    $this->fixture->close();

    $this->assertEquals(['test/' => null], $this->entriesWithContentIn($this->out));
  }

  #[Test]
  public function adding_files_and_dir() {
    $dir= $this->fixture->addDir(new ZipDirEntry('test/'));
    $this->fixture->addFile(new ZipFileEntry($dir, '1.txt'))->out()->write('File #1');
    $this->fixture->addFile(new ZipFileEntry($dir, '2.txt'))->out()->write('File #2');
    $this->fixture->close();

    $this->assertEquals(
      ['test/' => null, 'test/1.txt' => 'File #1', 'test/2.txt' => 'File #2'],
      $this->entriesWithContentIn($this->out)
    );
  }

  #[Test]
  public function using_password_protection() {
    $this->out= new MemoryOutputStream();

    $this->fixture= ZipFile::create($this->out)->usingPassword('secret');
    $this->fixture->addFile(new ZipFileEntry('test.txt'))->out()->write('File contents');
    $this->fixture->close();

    $this->assertEquals(['test.txt' => 'File contents'], $this->entriesWithContentIn($this->out, 'secret'));
  }

  #[Test, Expect(['class' => IllegalArgumentException::class, 'withMessage' => 'Filename too long (65536)'])]
  public function cannot_add_files_with_names_longer_than_65535_characters() {
    $this->fixture= ZipFile::create(new MemoryOutputStream());
    $this->fixture->addFile(new ZipFileEntry(str_repeat('n', 65535 + 1)));
  }

  #[Test, Expect(['class' => IllegalArgumentException::class, 'withMessage' => 'Filename too long (65536)'])]
  public function cannot_add_dirs_with_names_longer_than_65535_characters() {
    $this->fixture= ZipFile::create(new MemoryOutputStream());
    $this->fixture->addDir(new ZipDirEntry(str_repeat('n', 65535).'/'));
  }

  #[Test]
  public function using_unicode_names() {
    $this->out= new MemoryOutputStream();

    $this->fixture= ZipFile::create($this->out)->usingUnicodeNames();
    $this->fixture->addFile(new ZipFileEntry('関連事業調査.txt'))->out()->write('File contents');
    $this->fixture->close();

    $this->assertEquals(['関連事業調査.txt' => 'File contents'], $this->entriesWithContentIn($this->out, 'secret'));
  }
}