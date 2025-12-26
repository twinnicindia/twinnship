<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Facades\App\Repository\Orders;

class TestController extends Controller
{
    function index(){
        Order::all();
        dd('GOT');
    }
}
