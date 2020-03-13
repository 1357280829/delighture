<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        //
    }

    public function test2()
    {
        print_r(666);
        exit();
    }
}
