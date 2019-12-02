ZIP File support
================

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-framework/zip.svg)](http://travis-ci.org/xp-framework/zip)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 5.6+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_6plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/zip/version.png)](https://packagist.org/packages/xp-framework/zip)

Usage (creating a zip file)
---------------------------

```php
use io\archive\zip\ZipFile;
use io\archive\zip\ZipDirEntry;
use io\archive\zip\ZipFileEntry;
use io\File;

$z= ZipFile::create((new File('dist.zip'))->out());

// Add a directory
$dir= $z->add(new ZipDirEntry('META-INF'));

// Add a file
$file= $z->add(new ZipFileEntry($dir, 'version.txt'));
$file->out()->write($contents);

// Close
$z->close();
```

Usage (reading a zip file)
--------------------------

```php
use io\archive\zip\ZipFile;
use io\streams\Streams;
use io\File;

$z= ZipFile::open((new File('dist.zip'))->in());
foreach ($z->entries() as $entry) {
  if ($entry->isDirectory()) {
    // Create dir
  } else {
    // Extract
    Streams::readAll($entry->in());
  }
}
```