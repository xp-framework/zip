<?php namespace io\archive\zip\unittest;

/**
 * TestCase for malformed zip files
 */
class MalformedZipFileTest extends ZipFileTest {

  #[@test, @expect('lang.FormatException')]
  public function reading_zero_byte_long_file() {
    $this->entriesIn($this->archiveReaderFor('malformed', 'zerobytes'));
  }

  #[@test, @expect('lang.FormatException')]
  public function reading_file_with_incomplete_header() {
    $this->entriesIn($this->archiveReaderFor('malformed', 'pk'));
  }
}
