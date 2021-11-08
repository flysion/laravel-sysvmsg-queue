# laravel-sysvmsg-queue
使用 sysvmsg 作为消息队列，集成到 laravel 中。

需要 php 的 sysvmsg 扩展，sysvmsg 扩展文档见：https://www.php.net/manual/zh/book.sem.php

特点：

1. 存取作业的速度快（基于内存且无需网络通信）
2. 跨进程但不跨服务器
3. 容量小
4. 持久化

# 使用方法
1. 添加配置，示例如下：

        // queue.connections in config/queue.php
        'sysvmsg' => [
            'driver' => 'sysvmsg',
            'queue' => 1,
            'blocking' => true,
            'key_options' => [
                'type' => 'ftok',
                'filename' => __FILE__,
                'project_id' => env('SYSVMSG_PROJECT_ID'),
            ],
        ]
        
    | 参数 | 数据类 | 说明 |
    |:---|:---|:---|
    |queue|int| message type. See  [msg_send](https://www.php.net/manual/en/function.msg-send.php)、[msg_receive](https://www.php.net/manual/en/function.msg-receive.php) |
    |key_options.type| string | 队列的key生成方式：ftok,random,custom |
    |key_options.value | int | (key_options.type=custom) |
    |key_options.filename|string| (key_options.type=ftok)[fotk](https://www.php.net/manual/zh/function.ftok.php)的`filename`|
    |key_options.project_id|int| (key_options.type=ftok)[fotk](https://www.php.net/manual/zh/function.ftok.php)的`project_id`，取值在 1~255之间|
    |blocking|bool|[msg_send](https://www.php.net/manual/zh/function.msg-send.php) 的`blocking`|
    |flag|int|[msg_receive](https://www.php.net/manual/zh/function.msg-receive.php) 的`flag`|
        
2. 添加作业

        \App\Jobs\Example::dispath()->onConnection('sysvmsg')->onQueue(1/* 1~255 */)
        
3. 启动消费进程

        php artisan queue:work --queue=1 testttt