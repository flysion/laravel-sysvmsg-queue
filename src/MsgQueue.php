<?php

namespace Flysion\SysvMsgQueue;

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
    private $sysvMessageQueue;

    /**
     * @var int
     */
    public $queue;

    /**
     * @var string
     */
    public $filename;

    /**
     * @var string
     */
    public $projectId;

    /**
     * @var bool
     */
    public $blocking;

    /**
     * @var int
     */
    public $maxsize;

    /**
     * MsgQueue constructor.
     *
     * @param int $queue
     * @param string $filename
     * @param int $projectId
     * @param bool $blocking
     * @param int $maxsize
     */
    public function __construct($queue, $filename, $projectId, $blocking = true, $maxsize = 4096)
    {
        if($projectId <= 0 || $projectId > 255) {
            throw new Exception("[project_id] must be a one character string.");
        }

        $this->queue = $queue;
        $this->filename = $filename;
        $this->projectId = $projectId;
        $this->blocking = $blocking;
        $this->maxsize = $maxsize;

        $this->msgKey = ftok($this->filename, chr($this->projectId));
        $this->sysvMessageQueue = msg_get_queue($this->msgKey);
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        $stat = msg_stat_queue($this->sysvMessageQueue);
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
        return $this->pushRaw(
            $this->createPayload($job, $this->getQueue($queue), $data),
            $queue
        );
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
        return msg_send(
            $this->sysvMessageQueue,
            $this->getQueue($queue),
            $payload,
            true,
            $this->blocking
        );
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
            $this->sysvMessageQueue,
            $queue ?? 0,
            $srcMsgType,
            $this->maxsize,
            $msg,
            true
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
