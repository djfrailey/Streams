<?php

namespace Endl\Event;

use \RuntimeException;

trait Emitter
{
    protected $listeners = [];

    public function listen(string $event, $callable)
    {
        if (is_callable($callable) === false) {
            throw new RuntimeException("Second parameter must be a valid callable.");
        }
        
        $this->listeners[$event][] = $callable;
    }

    public function emit(string $event, ...$data)
    {
        if (isset($this->listeners[$event])) {
            foreach($this->listeners[$event] as $listener) {
                call_user_func_array($listener, $data);
            }
        }
    }
}