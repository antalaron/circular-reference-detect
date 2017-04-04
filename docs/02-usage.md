Usage
=====

Initialize the detector
-----------------------

Simple initiate the class.

```php
require __DIR__.'/vendor/autoload.php';

use Antalaron\Component\CircularReferenceDetect\CircularReferenceDetect;

$detector = new CircularReferenceDetect();
```

Or if you prefer the singleton, use it.

```php
require __DIR__.'/vendor/autoload.php';

use Antalaron\Component\CircularReferenceDetect\CircularReferenceDetect;

$detector = CircularReferenceDetect::newInstance();
```

Check if circular reference exists
----------------------------------

The `hasCircularReference()` returns the circle found first, or false, if there
is no circle.

```php
$a = [
    'a' => ['b'],
    'b' => ['c'],
    'c' => ['a'],
];

$b = [
    'a' => ['b'],
    'b' => ['c'],
];

(bool) $detector->hasCircularReference($a); // true
$detector->hasCircularReference($a); // ['a', 'b', 'c', 'a']

(bool) $detector->hasCircularReference($b); // false
```

Arguments of `hasCircularReference()`
-------------------------------------

Basically the first argument is an array, with a key/value pair, where the value
is an array, the elemnts of the value is referenced to the key.

```php
$a = [
    'a' => ['b', 'd', 'e'],
    'b' => ['c', 'd'],
    'c' => ['a'],
];

$detector->hasCircularReference($a);
````

You can also search for specific references as starting points, if you pass the
previous array az the second argument, and to the first argument an array of
starting points. It won't find a circle, because from the point of view of the
array `$b`, the circle cannot be reached.

```php
$a = [
    'a' => ['b'],
    'b' => ['c'],
    'c' => ['a'],
    'd' => ['e'],
    'e' => ['f'],
];
$b = [
    'd',
    'e',
];

$detector->hasCircularReference($b, $a); // false
```

Options
-------

There is a limit in the search (50), but you can set is with the first argument
of the constructor, or with the `setMaxDepth()` method.

The greater circles will not be found. And by default, the return value will be
false, unless shorter circle can be found.

You can configure that in case of out of limit the class throws an exception,
so you can check that even if circle weas not found, but there is a possibility
to one exists. Pass `true` to the second argument to the constructor, or
call `setThrowExceptionOnReachMaxDepth(true)`.
