ZIP File support for the XP Framework ChangeLog
========================================================================

## ?.?.? / ????-??-??

## 11.2.1 / 2025-11-20

* Fixed issue #4: *Undefined property: [...]ZipFileOutputStream::$data*
  when calling `close()` more than once.
  (@thekid)

## 11.2.0 / 2025-08-16

* Added compatibility with `xp-framework/math` version 10.0+ - @thekid
* Added compatibility with `xp-forge/compression` version 2.0+ - @thekid

## 11.1.0 / 2024-03-24

* Added dependency on `xp-forge/compression`, see xp-framework/rfc#342
  (@thekid)
* Made compatible with XP 12 - @thekid

## 11.0.0 / 2024-02-04

* Removed deprecated getInputStream() & getOutputStream() methods from
  `io.archive.zip.ZipFileEntry`
  (@thekid)
* Merged PR #3: Migrate to new testing library - @thekid

## 10.0.1 / 2022-02-26

* Fixed "Creation of dynamic property" warnings in PHP 8.2 - @thekid

## 10.0.0 / 2021-10-21

* Made `ZipFile::create()` and `ZipFile::open()` accept file names, 
  `io.Channel` instances as well as in- and output streams
  (@thekid)
* Implemented xp-framework/rfc#341, dropping compatibility with XP 9
  (@thekid)

## 9.0.1 / 2021-10-21

* Made compatible with PHP 8.1 - add `ReturnTypeWillChange` attributes to
  iterator, see https://wiki.php.net/rfc/internal_method_return_types
* Replaced xp::errorAt() call with a less-expensive check on iconv() return
  value. See xp-framework/core#299
  (@thekid)

## 9.0.0 / 2020-04-10

* Implemented xp-framework/rfc#334: Drop PHP 5.6:
  . **Heads up:** Minimum required PHP version now is PHP 7.0.0
  . Rewrote code base, grouping use statements
  . Converted `newinstance` to anonymous classes
  (@thekid)

## 8.0.2 / 2020-04-05

* Implemented RFC #335: Remove deprecated key/value pair annotation syntax
  (@thekid)

## 8.0.1 / 2019-12-02

* Made compatible with XP 10 - @thekid
* Replaced xp::stringOf() with util.Objects::stringOf() - @thekid

## 8.0.0 / 2017-09-24

* **Heads up**: Dropped PHP 5.5 support - @thekid
* Added compatibility with XP 9.0+ - @thekid
* Dropped dependency on `xp-framework/security` library - @thekid

## 7.1.0 / 2016-08-29

* Added forward compatibility with XP 8.0.0: Use File::in() instead of
  the deprecated *getInputStream()*
  (@thekid)

## 7.0.0 / 2016-02-21

* **Adopted semantic versioning. See xp-framework/rfc#300** - @thekid 
* Added version compatibility with XP 7 - @thekid

## 6.2.1 / 2016-01-24

* Fix code to use `nameof()` instead of the deprecated `getClassName()`
  method from lang.Generic. See xp-framework/core#120
  (@thekid)

## 6.2.0 / 2015-12-20

* **Heads up: Dropped PHP 5.4 support**. *Note: As the main source is not
  touched, unofficial PHP 5.4 support is still available though not tested
  with Travis-CI*.
  (@thekid)

## 6.1.0 / 2015-09-26

* Added PHP 7 support - @thekid
* Merged PR #2: Use short array syntax / ::class in annotations - @thekid

## 6.0.2 / 2015-07-12

* Added forward compatibility with XP 6.4.0 - @thekid

## 6.0.1 / 2015-02-12

* Changed dependency to use XP ~6.0 (instead of dev-master) - @thekid

## 6.0.0 / 2015-10-01

* Added `add()` method which calls addFile() or addDir() depending on the
  given type given.
  (@thekid)
* Added `in()` and `out()` methods as successors to the verbose forms 
  `getInputStream()` and `getOutputStream()`.
  (@thekid)
* Heads up: Converted classes to PHP 5.3 namespaces - (@thekid)
