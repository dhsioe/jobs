<?php
/**
 * @author: hsioe1111@gmail.com
 * @date: 2024/6/12
 * @description:
 */

namespace Hsioe\Jobs;

use support\exception\BusinessException;

abstract class HsioeJobsAbstract
{
    /**
     * 自定义调度方法
     * @param array $args
     * @param array $callable
     * @param int $delay
     * @return bool
     * @throws BusinessException
     */
    final public static function emit(array $args, array $callable, int $delay = 0): bool
    {
        if (2 !== count($callable)) {
            throw new BusinessException("hsioe-jobs callable params less than 2!");
        }
        
        list($class, $method) = $callable;
        
        return HsioeJobs::send([
            'job' => sprintf("%s@%s", $class, $method),
            'args' => $args
        ], $delay);
    }
    
    /**
     * 公共调度方法
     * @param array $args
     * @param int $delay
     * @return bool
     */
    final public static function dispatch(array $args, int $delay = 0): bool
    {
        $payload = [
            'job' => static::class . '@execute',
            'args' => $args
        ];
        
        return HsioeJobs::send($payload, $delay);
    }
    
    /**
     * 继承任务类需执行方法
     * @return void
     */
    abstract public function execute(): void;
}