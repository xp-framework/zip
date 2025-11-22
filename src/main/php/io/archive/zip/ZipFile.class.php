<?php namespace io\archive\zip;

use io\Channel;
use io\streams\{InputStream, FileInputStream, OutputStream, FileOutputStream};

/**
 * Zip archives hanadling
 *
 * Usage (creating a zip file)
 * ---------------------------
 * ```php
 * $z= ZipFile::create(new FileOutputStream(new File('dist.zip')));
 * $dir= $z->add(new ZipDirEntry('META-INF'));
 * $file= $z->addFile(new ZipFileEntry($dir, 'version.txt'));
 * $file->out()->write($contents);
 * $z->close();
 * ```
 *
 * Usage (reading a zip file)
 * --------------------------
 * ```php
 * $z= ZipFile::open(new FileInputStream(new File('dist.zip')));
 * foreach ($z->entries() as $entry) {
 *   if ($entry->isDirectory()) {
 *     // Create dir
 *   } else {
 *     // Extract
 *     Streams::readAll($entry->in());
 *   }
 * }
 * ```
 *
 * @test  io.archive.zip.unittest.vendors.InfoZipZipFileTest
 * @test  io.archive.zip.unittest.vendors.JarFileTest
 * @test  io.archive.zip.unittest.vendors.JavaZipFileTest
 * @test  io.archive.zip.unittest.vendors.Java7ZipFileTest
 * @test  io.archive.zip.unittest.vendors.PHPZipFileTest
 * @test  io.archive.zip.unittest.vendors.SevenZipFileTest
 * @test  io.archive.zip.unittest.vendors.WinRARZipFileTest
 * @test  io.archive.zip.unittest.vendors.WindowsZipFileTest
 * @test  io.archive.zip.unittest.vendors.XpZipFileTest
 * @test  io.archive.zip.unittest.MalformedZipFileTest
 * @test  io.archive.zip.unittest.ZipFileTest
 * @see   http://www.pkware.com/documents/casestudies/APPNOTE.TXT
 */
abstract class ZipFile {
  
  /**
   * Creation constructor
   *
   * @param  string|io.Path|io.Channel|io.streams.OutputStream $arg
   * @return io.archive.zip.ZipArchiveWriter
   */
  public static function create($arg) {
    if ($arg instanceof Channel) {
      return new ZipArchiveWriter($arg->out());
    } else if ($arg instanceof OutputStream) {
      return new ZipArchiveWriter($arg);
    } else {
      return new ZipArchiveWriter(new FileOutputStream($arg));
    }
  }

  /**
   * Read constructor
   *
   * @param  string|io.Path|io.Channel|io.streams.InputStream $arg
   * @return io.archive.zip.ZipArchiveReader
   */
  public static function open($arg) {
    if ($arg instanceof Channel) {
      return new ZipArchiveReader($arg->in());
    } else if ($arg instanceof InputStream) {
      return new ZipArchiveReader($arg);
    } else {
      return new ZipArchiveReader(new FileInputStream($arg));
    }
  }   
}