<?php

namespace Endl\Stream;

use \RuntimeException;

class Notifier
{
    private $callables = [];

    public function onStreamNotification()
    {
        foreach($this->callables as $callable) {
            call_user_func_array($callable['callback'], func_get_args());
        }
    }

    public function add($callable, int $priority = 0)
    {
        if (is_callable($callable) === false) {
            throw new RuntimeException("First argument must be a callable.");
        }

        $this->callables[] = [
            'callback' => $callable,
            'priority' => $priority
        ];

        $this->sortCallables();
    }

    private function sortCallables()
    {
        usort($this->callables, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }
}