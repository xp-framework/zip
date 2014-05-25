ZIP File support for the XP Framework
========================================================================

[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.3+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_3plus.png)](http://php.net/)

Usage (creating a zip file)
---------------------------

```php
use io\archive\zip\ZipFile;
use io\archive\zip\ZipDirEntry;
use io\archive\zip\ZipFileEntry;
use io\streams\FileOutputStream;
use io\File;

$z= ZipFile::create(new FileOutputStream(new File('dist.zip')));

// Add a directory
$z->addDir(new ZipDirEntry('META-INF'));

// Add a file
$e= $z->addFile(new ZipFileEntry('META-INF/version.txt'));
$e->getOutputStream()->write($contents);

// Close
$z->close();
```

Usage (reading a zip file)
--------------------------

```php
use io\archive\zip\ZipFile;
use io\streams\FileInputStream;
use io\streams\Streams;
use io\File;

$z= ZipFile::open(new FileInputStream(new File('dist.zip')));
foreach ($z->entries() as $entry) {
  if ($entry->isDirectory()) {
    // Create dir
  } else {
    // Extract
    Streams::readAll($entry->getInputStream());
  }
}
```