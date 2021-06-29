<?php

namespace Flysion\SysvMsgQueue;

use Illuminate\Contracts\Queue\ShouldQueue;

abstract class SimpleJob implements ShouldQueue
{
    /**
     * @var mixed
     */
    public $data;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    abstract public function handle();
}