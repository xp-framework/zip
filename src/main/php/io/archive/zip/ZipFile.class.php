<?php namespace io\archive\zip;

use io\streams\OutputStream;
use io\streams\InputStream;

/**
 * Zip archives hanadling
 *
 * Usage (creating a zip file)
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * <code>
 *   $z= ZipFile::create(new FileOutputStream(new File('dist.zip')));
 *   $z->addDir(new ZipDirEntry('META-INF'));
 *   $e= $z->addFile(new ZipFileEntry('META-INF/version.txt'));
 *   $e->getOutputStream()->write($contents);
 *   $z->close();
 * </code>
 *
 * Usage (reading a zip file)
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~
 * <code>
 *   $z= ZipFile::open(new FileInputStream(new File('dist.zip')));
 *   foreach ($z->entries() as $entry) {
 *     if ($entry->isDirectory()) {
 *       // Create dir
 *     } else {
 *       // Extract
 *       Streams::readAll($entry->getInputStream());
 *     }
 *   }
 * </code>
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
 * @see      http://www.pkware.com/documents/casestudies/APPNOTE.TXT
 */
abstract class ZipFile extends \lang\Object {
  
  /**
   * Creation constructor
   *
   * @param   io.streams.OutputStream stream
   * @return  io.archive.zip.ZipArchiveWriter
   */
  public static function create(OutputStream $stream) {
    return new ZipArchiveWriter($stream);
  }

  /**
   * Read constructor
   *
   * @param   io.streams.InputStream stream
   * @return  io.archive.zip.ZipArchiveReader
   */
  public static function open(InputStream $stream) {
    return new ZipArchiveReader($stream);
  }   
}
