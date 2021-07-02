<?php

namespace Flysion\SysvMsgQueue;

use Illuminate\Contracts\Queue\Queue as QueueContract;

class Queue extends \Illuminate\Queue\Queue implements QueueContract
{
    /**
     * @var resource
     */
    private $sysvmsg;

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
     * @var int
     */
    public $flag = 0;

    /**
     * Queue constructor.
     *
     * @param int $queue
     * @param string $filename
     * @param int $projectId
     * @param bool $blocking
     * @param int $maxsize
     */
    public function __construct($queue, $filename, $projectId, $blocking = true, $maxsize = 4096, $flag = 0)
    {
        if($projectId <= 0 || $projectId > 255) {
            throw new Exception("[project_id] must be a one character string.");
        }

        $this->queue = $queue;
        $this->filename = $filename;
        $this->projectId = $projectId;
        $this->blocking = $blocking;
        $this->maxsize = $maxsize;
        $this->flag = $flag;
    }

    /**
     * @link https://www.php.net/manual/zh/function.ftok.php
     * @link https://www.php.net/manual/zh/function.msg-get-queue.php
     * @return resource
     */
    public function sysvmsg()
    {
        if(!$this->sysvmsg) {
            $msgKey = ftok($this->filename, chr($this->projectId));
            $this->sysvmsg = msg_get_queue($msgKey);
        }

        return $this->sysvmsg;
    }

    /**
     * 重置队列
     * 该操作会清空队列消息
     *
     * @link https://www.php.net/manual/zh/function.msg-remove-queue.php
     */
    public function reset()
    {
        msg_remove_queue($this->sysvmsg());
        $this->sysvmsg = null;
    }

    /**
     * Get the size of the queue.
     *
     * @link https://www.php.net/manual/zh/function.msg-stat-queue.php
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        $stat = msg_stat_queue($this->sysvmsg());
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
     * @link https://www.php.net/manual/zh/function.msg-send.php
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        return msg_send(
            $this->sysvmsg(),
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
     * @link https://www.php.net/manual/zh/function.msg-receive.php
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $result = msg_receive(
            $this->sysvmsg(),
            $queue ?? 0,
            $srcMsgType,
            $this->maxsize,
            $msg,
            true,
            $this->flag
        );

        if ($result) {
            return new Job(
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
