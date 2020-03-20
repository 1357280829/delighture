<?php

namespace App\Http\Controllers\Api;

use App\Enums\Code;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'account' => 'required|between:6,12|unique:users,account',
            'password' => 'required|confirmed',
            'nickname' => 'required',
            'phone' => '',
            'email' => '',
        ]);

        User::create($validatedData);

        return $this->res(Code::Success);
    }
}