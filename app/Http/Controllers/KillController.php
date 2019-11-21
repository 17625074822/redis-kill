<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KillController extends Controller
{

    public function addUsers()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
    }
}
