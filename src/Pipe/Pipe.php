<?php

declare(strict_types=1);

namespace Endl\Pipe;

use \Psr\Http\Message\StreamInterface;
use \Endl\Stream\BlockingStream;
use \Endl\Stream\Factory as StreamFactory;

class Pipe
{
    private $bytesRead = 0;
    private $input;
    private $output;

    public function __construct(StreamInterface $input, StreamInterface $output)
    {
        $this->input = StreamFactory::createFromStream($input, true);
        $this->output = $output;

        $this->input->listen('onStreamRead', [$this, 'onInputReceived']);
    }

    public function getInput() : StreamInterface
    {
        return $this->input;
    }

    public function getOutput() : StreamInterface
    {
        return $this->output;
    }

    public function onInputReceived(string $data)
    {
        $this->output->write($data);
    }

    public function flush()
    {
        while ($this->input->eof() === false) {
            $this->input->read();
        }
    }

    public function close()
    {
        $this->input->close();
        $this->output->close();
    }
}