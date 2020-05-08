<?php

namespace App\Http\Controllers\Api;

use App\Enums\Code;
use App\Http\Controllers\Controller;
use App\Http\Queries\UserQuery;
use App\Jobs\SyncOneUserToES;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(UserQuery $query)
    {
        $users = $query->jsonPaginate();

        return $this->res(Code::Success, $users);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'account' => 'required|between:6,12|unique:users,account',
            'password' => 'required|confirmed',
            'nickname' => 'required',
            'phone' => 'unique:users',
            'email' => 'email',
        ]);

        $user = User::create($validatedData);

        //  用异步队列处理 Elasticsearch 的数据同步任务
        dispatch(new SyncOneUserToES($user));

        return $this->res(Code::Success);
    }
}