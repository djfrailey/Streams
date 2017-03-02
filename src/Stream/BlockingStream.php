<?php

declare(strict_types=1);

namespace Endl\Stream;

use \RuntimeException;

class BlockingStream extends Stream
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->setBlocking(true);
    }

    public function setBlocking(bool $mode = false)
    {
        if ($this->isBlocked() === true) {
            return;
        }

        if (stream_set_blocking($this->resource, $mode) === false) {
            throw new RuntimeException("Could not set stream to blocking.");
        }
    }

    public function isBlocked()
    {
        return $this->getMetadata('blocked');
    }
}