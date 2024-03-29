<?php namespace io\archive\zip;

use io\streams\compress\{
  Bzip2OutputStream,
  Bzip2InputStream,
  DeflatingOutputStream,
  InflatingInputStream
};
use io\streams\{InputStream, OutputStream};
use lang\{Enum, IllegalArgumentException};

/**
 * Compression algorithm enumeration.
 *
 * The following compression algorithms are defined by the standard:
 * ```
 *  0 - The file is stored (no compression)
 *  1 - The file is Shrunk
 *  2 - The file is Reduced with compression factor 1
 *  3 - The file is Reduced with compression factor 2
 *  4 - The file is Reduced with compression factor 3
 *  5 - The file is Reduced with compression factor 4
 *  6 - The file is Imploded
 *  7 - Reserved for Tokenizing compression algorithm
 *  8 - The file is Deflated
 *  9 - Enhanced Deflating using Deflate64(tm)
 * 10 - PKWARE Data Compression Library Imploding (old IBM TERSE)
 * 12 - File is compressed using BZIP2 algorithm
 * 14 - LZMA (EFS)
 * 18 - File is compressed using IBM TERSE (new)
 * 19 - IBM LZ77 z Architecture (PFS)
 * 97 - WavPack compressed data
 * 98 - PPMd version I, Rev 1
 * ```
 *
 * This implementation supports 0 (NONE), 8 (GZ) and 12 (BZIP2).
 *
 * @ext      bz2
 * @ext      zlib
 * @see      xp://io.archive.zip.ZipArchive
 * @test     xp://net.xp_framework.unittest.io.archive.CompressionTest
 * @purpose  Compressions
 */
abstract class Compression extends Enum {
  public static $NONE, $GZ, $BZ;
  
  static function __static() {
    self::$NONE= new class(0, 'NONE') extends Compression {
      static function __static() { }
      
      public function getCompressionStream(OutputStream $out, $level= 6) {
        return $out;
      }

      public function getDecompressionStream(InputStream $in) {
        return $in;
      }
    };
    self::$GZ= new class(8, 'GZ') extends Compression {
      static function __static() { }
      
      public function getCompressionStream(OutputStream $out, $level= 6) {
        return new DeflatingOutputStream($out, $level);
      }

      public function getDecompressionStream(InputStream $in) {
        return new InflatingInputStream($in);
      }
    };
    self::$BZ= new class(12, 'BZ') extends Compression {
      static function __static() { }
      
      public function getCompressionStream(OutputStream $out, $level= 6) {
        return new Bzip2OutputStream($out, $level);
      }

      public function getDecompressionStream(InputStream $in) {
        return new Bzip2InputStream($in);
      }
    };
  }
  
  /**
   * Gets compression stream. Implemented in members.
   *
   * @param   io.streams.OutputStream out
   * @param   int level default 6 the compression level
   * @return  io.streams.OutputStream
   * @throws  lang.IllegalArgumentException if the level is not between 0 and 9
   */
  public abstract function getCompressionStream(OutputStream $out, $level= 6);

  /**
   * Gets decompression stream. Implemented in members.
   *
   * @param   io.streams.InputStream in
   * @return  io.streams.InputStream
   */
  public abstract function getDecompressionStream(InputStream $in);

  /**
   * Get a compression instance by a given id
   *
   * @param   int n
   * @return  io.archive.zip.Compression
   * @throws  lang.IllegalArgumentException
   */
  public static function getInstance($n) {
    switch ($n) {
      case 0: return self::$NONE;
      case 8: return self::$GZ;
      case 12: return self::$BZ;
      default: throw new IllegalArgumentException('Unknown compression algorithm #'.$n);
    }
  }
}