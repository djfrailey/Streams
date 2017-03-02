<?php

namespace Endl\Stream;

use \Psr\Http\Message\StreamInterface;
use \RuntimeException;

class Factory
{
    public static function createFromStream(StreamInterface $stream, bool $blocking = false)
    {
        return self::createFromResource($stream->getResource(), $blocking);
    }

    public static function createFromResource($resource, bool $blocking = false)
    {
        if (is_resource($resource) === false) {
            throw new RuntimeException("Argument 1 must be a resource. " . gettype($resource) . " given.");
        }

        if ($blocking) {
            return new BlockingStream($resource);
        }

        return new Stream($resource);
    }

    public static function createFromString(string $string, string $mode, bool $blocking = false)
    {
        $resource = fopen($string, $mode, false);

        if ($resource === false) {
            throw new RuntimeException("Error creating stream. Could not open $string.");
        }

        if ($blocking) {
            return new BlockingStream($resource);
        }

        return new Stream($resource);
    }
}