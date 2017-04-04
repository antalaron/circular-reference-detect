Circular reference detect
=========================

[![Build Status](https://travis-ci.org/antalaron/circular-reference-detect.svg?branch=master)](https://travis-ci.org/antalaron/circular-reference-detect) [![Coverage Status](https://coveralls.io/repos/github/antalaron/circular-reference-detect/badge.svg)](https://coveralls.io/github/antalaron/circular-reference-detect?branch=master) [![Latest Stable Version](https://poser.pugx.org/antalaron/circular-reference-detect/v/stable)](https://packagist.org/packages/antalaron/circular-reference-detect) [![Latest Unstable Version](https://poser.pugx.org/antalaron/circular-reference-detect/v/unstable)](https://packagist.org/packages/antalaron/circular-reference-detect) [![License](https://poser.pugx.org/antalaron/circular-reference-detect/license)](https://packagist.org/packages/antalaron/circular-reference-detect)

PHP library to detect reference circular references in array.

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
    'a' => ['b'],
    'b' => ['c'],
    'c' => ['a'],
];
$detector = new CircularReferenceDetect();
$detector->hasCircularReference($a);
```

Documentation
-------------

1. [Installation](01-installation.md)
2. [Usage](02-usage.md)
3. [Contributing](03-contributing.md)

License
-------

This library is under [MIT License](http://opensource.org/licenses/mit-license.php).
