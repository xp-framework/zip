<?php namespace io\archive\zip;

use io\streams\OutputStream;

/** Needs explicit closing */
class RemainsOpen implements OutputStream {
  private $out;

  public function __construct(OutputStream $out) { $this->out= $out; }

  public function write($bytes) { $this->out->write($bytes); }

  public function flush() { $this->out->flush(); }

  public function close($force= false) { $force && $this->out->close(); }
}