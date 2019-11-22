<?php

use Illuminate\Http\Request;
use App\Product;
use App\User;
use App\Jobs\CreateOrder;
use Illuminate\Support\Facades\Validator;

const LIST_KEY = "list";
const ORDER_KEY = "orders";
const FAILUSERNUM = "fail_users";

//商品加入秒杀活动
Route::get('/addgoods/{product}', function (Product $product) {
    //连接redis
    $redis = RedisConnection();
    for ($i = 1; $i <= $product->nums; $i++) {
        $redis->rPush(LIST_KEY . $product->id, $product->id);
    }
    return response()->json([
        'data' => '商品已加入秒杀活动,库存为' . $product->nums
    ]);
});
Route::post('/kill', function (Request $request) {
//测试用
//    $product_id = 1;
//    $user_id = rand(1, 999999999);
    $user_id = uniqid('user').time();
    $input = $request->all();
    //链接redis
    $redis = RedisConnection();
//    $user_id = $request->id;
    if ($product_id = $redis->lPop(LIST_KEY.$input['product_id'])) {
        $redis->hSet(ORDER_KEY, $user_id, $product_id);
//        dispatch(new CreateOrder($user_id, $product_id)); //全局函数 派发任务给队列
    } else {
        $redis->incr(FAILUSERNUM);
        return response()->json([
            'data' => '很遗憾,你没抢到!'
        ]);
    }
    return response()->json([
        'data' => '抢购成功!'
    ]);
});

//连接redis
function RedisConnection()
{
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->auth('123456');
    return $redis;
}
