# laravel-sysvmsg-queue
使用 sysvmsg 作为消息队列，集成到 laravel 中。

需要 php 的 sysvmsg 扩展，sysvmsg 扩展文档见：https://www.php.net/manual/zh/book.sem.php

特点：

1. 存取作业的速度快
2. 跨进程（但不跨服务器）
3. 容量小，具体见：
4. 持久化（只要服务器不重启作业就不会丢失）

# 使用方法
1. 添加配置，示例如下：

        // queue.connections in config/queue.php
        'testttt' => [
            'driver' => 'sysvmsg',
            'queue' => 1,
            'filename' => __FILE__,
            'project_id' => 1,
            'blocking' => true,
            'flag' => 0,
        ]
        
    | 参数 | 数据类 | 说明 |
    |:---|:---|:---|
    |queue|int|大于0的整数，默认的推送队列|
    |filename|string| [fotk](https://www.php.net/manual/zh/function.ftok.php)的`filename`|
    |project_id|int| [fotk](https://www.php.net/manual/zh/function.ftok.php)的`project_id`，取值在 1~255之间|
    |blocking|bool|[msg_send](https://www.php.net/manual/zh/function.msg-send.php)的`blocking`|
    |flag|int|[msg_receive](https://www.php.net/manual/zh/function.msg-receive.php)的`flag`|
        
2. 添加作业

        \App\Jobs\Example::dispath()->onConnection('testttt')->onQueue(1)
        
3. 启动消费进程

        php artisan queue:work --queue=1 testttt