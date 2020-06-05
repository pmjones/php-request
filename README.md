# pmjones/request

This package provides a PHP 5.x userland implementation of the PECL
[`request`](http://pecl.php.net/request) extension (which is for PHP 7.x).

Usage is identical to that of the extension, with these exceptions:

- The `SapiRequest::__get()` etc. magic methods are declared as `final`; this
  is to help mitigate attempts to subvert immutability.

- The `SapiRequest::$content` property is populated at instantiation time,
  instead of at the time of first access.

You can read the documentation at <https://github.com/pmjones/ext-request>.

To run the tests, issue `composer test` at the package root.
