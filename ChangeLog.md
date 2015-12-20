ZIP File support for the XP Framework ChangeLog
========================================================================

## ?.?.? / ????-??-??

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
