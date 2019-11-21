<?php

use Illuminate\Http\Request;
use App\Product;
use App\User;
use App\Jobs\CreateOrder;
use Illuminate\Support\Facades\Validator;

//商品加入秒杀活动
Route::get('/addgoods/{product}', function (Product $product) {
    $listKey = "products";
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->auth('123456');
    for ($i = 1; $i <= $product->nums; $i++) {
        $redis->rPush($listKey, $i);
    }
    return response()->json([
        'data' => '商品已加入秒杀活动,库存为' . $product->nums
    ]);
});

Route::get('/kill', function () {
//测试用
//    $product_id = 1;
//    $user_id = rand(1, 999999999);

    $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer  ',
        'product_id' => 'required|integer',
    ]);
    $messages = [
        'user_id.required' => '用户id是必填的',
        'user_id.integer' => '用户id是数字',
        'product_id.integer' => '商品id是必填的',
        'product_id.integer' => '商品id是数字',
    ];
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->auth('123456');
    $listKey = "products";
    $orderKey = "orders";
    $failUserNum = "fail_users";
    if (!Product::find($request->product_id)) {
        return response()->json([
            'data' => '商品id不存在'
        ]);
    }
    if (!User::find($request->user_id)) {
        return response()->json([
            'data' => '用户id不存在'
        ]);
    }
    if ($redis->lPop($listKey)) {
        $redis->hSet($orderKey, $request->user_id, $request->product_id);
        dispatch(new CreateOrder($request->user_id, $request->product_id)); //全局函数 派发任务给队列
    } else {
        $redis->incr($failUserNum);
        return response()->json([
            'data' => '很遗憾,你没抢到!'
        ]);
    }
    $user_id++;
    return response()->json([
        'data' => '抢购成功!'
    ]);
});
