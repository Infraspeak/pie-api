<?php

namespace App\Console\Commands;

use App\Events\AppError;
use App\Events\ParsedFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisErrorsSubscriber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe:errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to the Redis APP_ERRORS channel';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->info('Subscribing topic APP_ERRORS');

        Redis::subscribe(['APP_ERRORS'], function ($message) {
            echo $message . \PHP_EOL;
            $message = json_decode($message);
            return broadcast(new AppError($message))->toOthers();
        });
    }
}
