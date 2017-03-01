<?php

require_once('vendor/autoload.php');

use Endl\Stream\Stream;

$stream = new Stream(fopen('php://output', 'w'));
$stream->write("Hello World!\r\n");
$stream->close();