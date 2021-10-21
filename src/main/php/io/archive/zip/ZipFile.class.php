<?php namespace io\archive\zip;

use io\Channel;
use io\streams\{InputStream, FileInputStream, OutputStream, FileOutputStream};

/**
 * Zip archives hanadling
 *
 * Usage (creating a zip file)
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * ```php
 * $z= ZipFile::create(new FileOutputStream(new File('dist.zip')));
 * $dir= $z->add(new ZipDirEntry('META-INF'));
 * $file= $z->addFile(new ZipFileEntry($dir, 'version.txt'));
 * $file->out()->write($contents);
 * $z->close();
 * ```
 *
 * Usage (reading a zip file)
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~
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
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.InfoZipZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.JarFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.JavaZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.Java7ZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.PHPZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.SevenZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.WinRARZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.WindowsZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.vendors.XpZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.MalformedZipFileTest
 * @test     xp://net.xp_framework.unittest.io.archive.ZipFileTest
 * @see      http://www.pkware.com/documents/casestudies/APPNOTE.TXT
 */
abstract class ZipFile {
  
  /**
   * Creation constructor
   *
   * @param   string|io.Channel|io.streams.OutputStream $arg
   * @return  io.archive.zip.ZipArchiveWriter
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
   * @param   string|io.Channel|io.streams.InputStream $arg
   * @return  io.archive.zip.ZipArchiveReader
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