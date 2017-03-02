<?php

declare(strict_types=1);

require_once('vendor/autoload.php');

use Endl\Stream\Factory;
use Endl\Pipe\Pipe;


$input = Factory::createFromString('http://www.google.com', 'r');
$output = Factory::createFromSTring('google.txt', 'w');
$pipe = new Pipe($input, $output);
$pipe->flush();
$pipe->close();