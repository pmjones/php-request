<?php
declare(strict_types=1);

class SapiUpload
{
    public /* readonly ?string */ $name;

    public /* readonly ?string */ $type;

    public /* readonly ?int */ $size;

    public /* readonly ?string */ $tmpName;

    public /* readonly ?int */ $error;

    public function __construct(
        ?string $name,
        ?string $type,
        ?int $size,
        ?string $tmpName,
        ?int $error
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->tmpName = $tmpName;
        $this->error = $error;
    }

    final public function __get(string $key) // : mixed
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        $class = get_class($this);
        throw new RuntimeException("{$class}::\${$key} does not exist.");
    }

    final public function __set(string $key, $val) : void
    {
        $class = get_class($this);

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
