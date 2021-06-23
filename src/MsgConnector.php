<?php

namespace Flysion\MsgQueue;

use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Queue\RedisQueue;
use \Illuminate\Queue\Connectors\ConnectorInterface;

class MsgConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new MsgQueue(
            $config['queue'],
            $config['ftok_filename'],
            $config['ftok_project_id']
        );
    }
}
