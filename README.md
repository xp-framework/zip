ZIP File support
================

[![Build status on GitHub](https://github.com/xp-framework/zip/workflows/Tests/badge.svg)](https://github.com/xp-framework/zip/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/zip/version.png)](https://packagist.org/packages/xp-framework/zip)

Usage (creating a zip file)
---------------------------

```php
use io\archive\zip\{ZipFile, ZipDirEntry, ZipFileEntry};
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