<?php
/**
 * @author: hsioe1111@gmail.com
 * @date: 2024/6/12
 * @description:
 */

namespace Hsioe\Jobs;


use support\Container;
use Webman\RedisQueue\Client;
use Webman\RedisQueue\Redis;

class HsioeJobs
{
    
    /**
     * 推送任务
     * @param array $data
     * @param int $delay
     * @return void
     */
    public static function send(array $data, int $delay = 0): bool
    {
        return Redis::send(config('plugin.hsioe-job.app.queue_name'), $data, $delay);
    }
    
    /**
     * 进程启动
     * @return void
     */
    public function onWorkerStart(): void
    {
        Client::connection()->subscribe(config('plugin.hsioe-job.app.queue_name'), function (array $payload) {
            // 监听队列数据
            $job = $payload['job'] ?? '';
            $data = $payload['args'] ?? null;
            $constructor = $payload['constructor'] ?? [];
            if (empty($job)) {
                return;
            }
            
            list($class, $method) = explode('@', $job);
            $instance = $constructor ? Container::make($class, $constructor) : Container::get($class);
            if ($instance && method_exists($instance, $method)) {
                if (is_array($data)) {
                    // 数组
                    $instance->{$method}(...array_values($data));
                } else {
                    // null/int/bool/string
                    $instance->{$method}($data);
                }
            }
        });
    }
    
    
    public function onWorkerStop(): void
    {
        // 进程退出后
    }
}