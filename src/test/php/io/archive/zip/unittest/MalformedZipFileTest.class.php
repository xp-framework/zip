<?php namespace io\archive\zip\unittest;

use lang\FormatException;

/**
 * TestCase for malformed zip files
 */
class MalformedZipFileTest extends AbstractZipFileTest {

  #[@test, @expect(FormatException::class)]
  public function reading_zero_byte_long_file() {
    $this->entriesIn($this->archiveReaderFor('malformed', 'zerobytes'));
  }

  #[@test, @expect(FormatException::class)]
  public function reading_file_with_incomplete_header() {
    $this->entriesIn($this->archiveReaderFor('malformed', 'pk'));
  }
}
