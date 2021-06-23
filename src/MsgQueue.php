<?php

namespace Flysion\MsgQueue;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Support\Str;
use Illuminate\Queue\Queue;

class MsgQueue extends Queue implements QueueContract
{
    /**
     * @var int
     */
    private $msgKey;

    /**
     * @var resource
     */
    private $msgQueue;

    /**
     * @var int
     */
    public $queue;

    /**
     * @var string
     */
    public $ftokFilename;

    /**
     * @var string
     */
    public $ftokProjectId;

    /**
     * MsgQueue constructor.
     *
     * @param int $queue
     * @param string $ftokFilename
     * @param string $ftokProjectId
     */
    public function __construct($queue, $ftokFilename, $ftokProjectId)
    {
        $this->queue = $queue;
        $this->ftokFilename = $ftokFilename;
        $this->ftokProjectId = $ftokProjectId;

        $this->msgKey = ftok($this->ftokFilename, $this->ftokProjectId);
        $this->msgQueue = msg_get_queue($this->msgKey);
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        $stat = msg_stat_queue($this->msgQueue);
        return $stat ? $stat['msg_qnum'] : 0;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  object|string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $this->getQueue($queue), $data), $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        return msg_send($this->msgQueue, $this->getQueue($queue), $payload, true, true);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  object|string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {

    }

    /**
     * Push a raw job onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $payload
     * @param  string|null  $queue
     * @return mixed
     */
    protected function laterRaw($delay, $payload, $queue = null)
    {

    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $ret = msg_receive(
            $this->msgQueue,
            $queue ?? 0,
            $srcMsgType, 4096,
            $msg,
            true,
            MSG_IPC_NOWAIT
        );

        if ($ret) {
            return new MsgJob(
                $this->container, $this, $msg, $this->connectionName, $srcMsgType
            );
        }
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ?: $this->queue;
    }
}
