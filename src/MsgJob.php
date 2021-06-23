<?php

namespace Flysion\MsgQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\RedisQueue;

class MsgJob extends \Illuminate\Queue\Jobs\Job implements JobContract
{
    /**
     * @var  MsgQueue
     */
    protected $msgQueue;

    /**
     * @var string
     */
    protected $job;

    /**
     * Create a new job instance.
     *
     * @param  Container $container
     * @param  MsgQueue  $msgQueue
     * @param  string  $job
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(Container $container, $msgQueue, $job, $connectionName, $queue)
    {
        $this->container = $container;
        $this->msgQueue = $msgQueue;
        $this->job = $job;
        $this->connectionName = $connectionName;
        $this->queue = $queue;
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->job;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        return ;
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return 1;
    }

    /**
     * Get the job identifier.
     *
     * @return string|null
     */
    public function getJobId()
    {
        return '';
    }
}
