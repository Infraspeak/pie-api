<?php

namespace App\Console\Commands;

use App\Events\ParsedFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisSubscriber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe:issues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Subscribing topic ISSUES');

        Redis::subscribe(['ISSUES'], function ($message) {
            echo $message . \PHP_EOL;
            $message = json_decode($message);
            return broadcast(new ParsedFile($message))->toOthers();
        });
    }
}
