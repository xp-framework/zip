ZIP File support for the XP Framework ChangeLog
========================================================================

## ?.?.? / ????-??-??

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
