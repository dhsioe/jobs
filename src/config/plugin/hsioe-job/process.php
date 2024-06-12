<?php
/**
 * @author: hsioe1111@gmail.com
 * @date: 2024/6/12
 * @description:
 */

use Hsioe\Jobs\HsioeJobs;

return [
    // 任务队列进程
    'hsioe-jobs' => [
        'handler' => HsioeJobs::class,
        'count' => 8
    ]
];