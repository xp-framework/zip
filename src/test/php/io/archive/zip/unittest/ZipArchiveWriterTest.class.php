<?php namespace io\archive\zip\unittest;

use io\archive\zip\{ZipArchiveWriter, ZipDirEntry, ZipFile, ZipFileEntry};
use io\streams\{MemoryInputStream, MemoryOutputStream, StreamTransfer};
use lang\IllegalArgumentException;
use test\{Assert, Expect, Test, Values};
use util\Secret;

class ZipArchiveWriterTest extends AbstractZipFileTest {

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.streams.MemoryOutputStream $out
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
    $out= new MemoryOutputStream();

    $fixture= new ZipArchiveWriter($out);
    $fixture->addFile(new ZipFileEntry('test.txt'))->out()->write('File contents');
    $fixture->close();

    Assert::equals(['test.txt' => 'File contents'], $this->entriesWithContentIn($out));
  }

  #[Test]
  public function adding_a_file_via_add() {
    $out= new MemoryOutputStream();

    $fixture= new ZipArchiveWriter($out);
    $fixture->add(new ZipFileEntry('test.txt'))->out()->write('File contents');
    $fixture->close();

    Assert::equals(['test.txt' => 'File contents'], $this->entriesWithContentIn($out));
  }

  #[Test]
  public function adding_a_dir_via_addDir() {
    $out= new MemoryOutputStream();

    $fixture= new ZipArchiveWriter($out);
    $fixture->addDir(new ZipDirEntry('test'));
    $fixture->close();

    Assert::equals(['test/' => null], $this->entriesWithContentIn($out));
  }

  #[Test]
  public function adding_a_dir_via_add() {
    $out= new MemoryOutputStream();

    $fixture= new ZipArchiveWriter($out);
    $fixture->add(new ZipDirEntry('test'));
    $fixture->close();

    Assert::equals(['test/' => null], $this->entriesWithContentIn($out));
  }

  #[Test]
  public function adding_files_and_dir() {
    $out= new MemoryOutputStream();
    $fixture= new ZipArchiveWriter($out);
    $dir= $fixture->addDir(new ZipDirEntry('test/'));
    $fixture->addFile(new ZipFileEntry($dir, '1.txt'))->out()->write('File #1');
    $fixture->addFile(new ZipFileEntry($dir, '2.txt'))->out()->write('File #2');
    $fixture->close();

    Assert::equals(
      ['test/' => null, 'test/1.txt' => 'File #1', 'test/2.txt' => 'File #2'],
      $this->entriesWithContentIn($out)
    );
  }

  #[Test, Values(['secret', new Secret('secret')])]
  public function using_password_protection($password) {
    $out= new MemoryOutputStream();

    $fixture= ZipFile::create($out)->usingPassword($password);
    $fixture->addFile(new ZipFileEntry('test.txt'))->out()->write('File contents');
    $fixture->close();

    Assert::equals(['test.txt' => 'File contents'], $this->entriesWithContentIn($out, 'secret'));
  }

  #[Test, Expect(class: IllegalArgumentException::class, message: 'Filename too long (65536)')]
  public function cannot_add_files_with_names_longer_than_65535_characters() {
    $fixture= ZipFile::create(new MemoryOutputStream());
    $fixture->addFile(new ZipFileEntry(str_repeat('n', 65535 + 1)));
  }

  #[Test, Expect(class: IllegalArgumentException::class, message: 'Filename too long (65536)')]
  public function cannot_add_dirs_with_names_longer_than_65535_characters() {
    $fixture= ZipFile::create(new MemoryOutputStream());
    $fixture->addDir(new ZipDirEntry(str_repeat('n', 65535).'/'));
  }

  #[Test]
  public function using_unicode_names() {
    $out= new MemoryOutputStream();

    $fixture= ZipFile::create($out)->usingUnicodeNames();
    $fixture->addFile(new ZipFileEntry('関連事業調査.txt'))->out()->write('File contents');
    $fixture->close();

    Assert::equals(['関連事業調査.txt' => 'File contents'], $this->entriesWithContentIn($out, 'secret'));
  }

  #[Test]
  public function transferring_a_file() {
    $out= new MemoryOutputStream();
    $fixture= new ZipArchiveWriter($out);
    $file= $fixture->add(new ZipFileEntry('test.txt'));

    $transfer= new StreamTransfer(new MemoryInputStream('File contents'), $file->out());
    $transfer->transferAll();
    $transfer->close();

    $fixture->close();

    Assert::equals(['test.txt' => 'File contents'], $this->entriesWithContentIn($out));
  }

  #[Test]
  public function central_directory_added_only_once() {
    $out= new MemoryOutputStream();

    $fixture= new ZipArchiveWriter($out);
    $fixture->close();
    $size= strlen($out->bytes());
    $fixture->close();

    Assert::equals($size, strlen($out->bytes()));
  }
}