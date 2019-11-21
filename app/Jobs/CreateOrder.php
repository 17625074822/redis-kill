<?php

namespace App\Jobs;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user_id;
    public $product_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $product_id)
    {
        $this->user_id = $user_id;
        $this->product_id = $product_id;
    }

    /**
     * 把集合的数据插入数据库
     *
     * @return void
     */
    public function handle()
    {

        Order::create([
            'order_id' => Str::random(32),
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
        ]);

    }

    public function failed(\Exception $exception)
    {
        Log::error($exception);
    }
}
