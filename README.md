# pmjones/request

This package provides a PHP 5.x userland implementation of the PECL `request`
extension. Usage is identical to that of the extension, with these exceptions:

- The `ServerRequest::__get()` etc. magic methods are declared as `final`; this
  is to help mitigate attempts to subvert immutability.

- The `ServerRequest::$content` property is populated at instantiation time,
  instead of at the time of first access.

You can read the documentation at <https://gitlab.com/pmjones/ext-request>.
