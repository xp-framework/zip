<?php namespace io\archive\zip\unittest;

use lang\FormatException;
use test\{Expect, Test};

class MalformedZipFileTest extends AbstractZipFileTest {

  #[Test, Expect(FormatException::class)]
  public function reading_zero_byte_long_file() {
    $this->entriesIn($this->archiveReaderFor('malformed', 'zerobytes'));
  }

  #[Test, Expect(FormatException::class)]
  public function reading_file_with_incomplete_header() {
    $this->entriesIn($this->archiveReaderFor('malformed', 'pk'));
  }
}