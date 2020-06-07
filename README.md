# pmjones/request

This package provides a userland implementation of the PECL
[`request`](http://pecl.php.net/request) extension, version 2.

Usage is identical to that of the extension, with these exceptions:

- The `SapiRequest` and `SapiUpload` magic methods (`__get()`, etc.) are
  declared as `final`; this is to help mitigate attempts to subvert
  immutability.

You can read the documentation at <https://github.com/pmjones/ext-request>.

To run the tests, issue `composer test` at the package root.
