Circular reference detect
==========================

Library to detect reference circular references in array.

Installation
------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this library:

```bash
$ composer require antalaron/circular-reference-detect
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Usage
-----

Find a circular reference in an array:

```php
require __DIR__.'/vendor/autoload.php';

use Antalaron\Component\CircularReferenceDetect\CircularReferenceDetect;
$a = [
    '*' => ['a'],
    'a' => ['b'],
    'b' => ['c'],
    'c' => ['a'],
];
$detector = new CircularReferenceDetect();
$detector->hasCircularReference($a);
```

License
-------

This library is under [MIT License](http://opensource.org/licenses/mit-license.php).
