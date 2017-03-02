<?php

declare(strict_types=1);

namespace Endl\Stream;

use \Psr\Http\Message\StreamInterface;
use \RuntimeException;

class Stream implements StreamInterface
{
    use \Endl\Event\Emitter;

    protected $resource;

    const STREAM_READABLE = 1;
    const STREAM_WRITABLE = 2;
    const STREAM_BINARY = 4;
    
    protected $modes = [
        'r'  => self ::STREAM_READABLE,
        'r+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'w'  => self ::STREAM_WRITABLE,
        'w+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'a'  => self ::STREAM_READABLE,
        'a+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'x'  => self ::STREAM_READABLE,
        'x+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'c'  => self ::STREAM_WRITABLE,
        'c+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'wb' => self::STREAM_WRITABLE | self::STREAM_BINARY,
        'rb' => self::STREAM_READABLE | self::STREAM_BINARY,
    ];

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function __toString()
    {
        $this->seek(0, SEEK_SET);
        return stream_get_contents($this->resource);
    }

    public function close()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    public function detach()
    {
        $copy = $this->resource;

        $this->resource = null;

        return $copy;
    }

    public function getSize()
    {
        $size = null;

        if ($this->isSeekable()) {
            $this->seek(0, SEEK_END);
            $size = $this->tell();
        }

        return $size;
    }

    public function tell() : int
    {
        return ftell($this->resource);
    }

    public function eof() : bool
    {
        return feof($this->resource);
    }

    public function isSeekable() : bool
    {
        return $this->getMetadata('seekable');
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if ($this->isSeekable() === false) {
            throw new RuntimeException("Stream is not seekable.");
        }

        return fseek($this->resource, $$offset, $whence);
    }

    public function rewind()
    {
        if ($this->isSeekable() === false) {
            throw new RuntimeException("Cannot rewind stream. Stream is not seekable.");
        }

        $this->seek(0, SEEK_SET);
    }

    public function isWritable()
    {
        $mode = $this->getMetadata('mode');
        return $this->modes[$mode] & self::STREAM_WRITABLE;
    }

    public function write($string)
    {
        if ($this->isWritable() === false) {
            throw new RuntimeException("Cannot write data to stream. Stream is not writable.");
        }

        $written = fwrite($this->resource, $string);

        if ($written === false) {
            throw new RuntimeException("There was an error while writing to the stream.");
        }

        return $written;
    }
    
    public function isReadable() 
    {
        $mode = $this->getMetadata('mode');
        return $this->modes[$mode] & self::STREAM_READABLE;
    }
    
    public function read($length = 1024)
    {
        $read = fread($this->resource, $length);

        if ($read === false) {
            throw new RuntimeException("There was an error while reading from the stream.");
        }

        $this->emit('onStreamRead', $read);

        return $read;
    }

    public function getContents() 
    {
        $contents = stream_get_contents($this->resource);

        if ($contents === false) {
            throw new RuntimeException("There was an error retrieving the contents of the stream.");
        }

        return $contents;
    }

    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->resource);

        if (is_null($key) === true) {
            return $meta;
        }

        if (isset($meta[$key])) {
            return $meta[$key];
        }

        return null;
    }

    public function getResource()
    {
        return $this->resource;
    }
}