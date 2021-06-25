<?php

namespace Flysion\SysvMsgQueue;

use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Queue\RedisQueue;
use \Illuminate\Queue\Connectors\ConnectorInterface;

class Connector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new Queue(
            $config['queue'],
            $config['filename'],
            intval($config['project_id']),
            $config['blocking'] ?? true,
            $config['maxsize'] ?? 4096,
            $config['flag'] ?? 0
        );
    }
}
