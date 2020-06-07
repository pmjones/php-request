<?php
declare(strict_types=1);

/**
 * Read-only SAPI file-upload object.
 *
 * @property-read $name
 * @property-read $type
 * @property-read $size
 * @property-read $tmpName
 * @property-read $error
 */
class SapiUpload
{
    private /* bool */ $isUnconstructed = true;

    private /* readonly ?string */ $name;
    private /* readonly ?string */ $type;
    private /* readonly ?int */ $size;
    private /* readonly ?string */ $tmpName;
    private /* readonly ?int */ $error;

    public function __construct(
        ?string $name,
        ?string $type,
        ?int $size,
        ?string $tmpName,
        ?int $error
    ) {
        if (! $this->isUnconstructed) {
            $class = get_class($this);
            throw new RuntimeException("{$class}::__construct() called after construction.");
        }

        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->tmpName = $tmpName;
        $this->error = $error;

        $this->isUnconstructed = false;
    }

    final public function __get(string $key) // : mixed
    {
        if ($key === 'content') {
            return $this->content ?? file_get_contents('php://input');
        }

        if (property_exists($this, $key)) {
            return $this->$key;
        }

        $class = get_class($this);
        throw new RuntimeException("{$class}::\${$key} does not exist.");
    }

    final public function __set(string $key, $val) : void
    {
        $class = get_class($this);

        // problem is that extended classes
        // cannot get their proprties set from the outside,
        // as if they are public
        if (property_exists($this, $key)) {
            throw new RuntimeException("{$class}::\${$key} is read-only.");
        }

        throw new RuntimeException("{$class}::\${$key} does not exist.");
    }

    final public function __isset(string $key) : bool
    {
        if (property_exists($this, $key)) {
            return isset($this->$key);
        }

        return false;
    }

    final public function __unset(string $key) : void
    {
        if (property_exists($this, $key)) {
            $class = get_class($this);
            throw new RuntimeException("{$class}::\${$key} is read-only.");
        }
    }

    final public function move(string $destination) : bool
    {
        return move_uploaded_file($this->tmpName, $destination);
    }
}
