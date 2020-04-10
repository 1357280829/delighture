<?php

namespace App\Http\Controllers\Api;

use App\Enums\Code;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Jobs\RecordLastLoginAt;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends Controller
{
    public function store(AuthorizationRequest $request)
    {
        $token = Auth::attempt($request->only(['account', 'password']));
        if (!$token) {
            throw new CustomException(Code::getDescription(Code::FailedLogin), Code::FailedLogin);
        }

        $request->user()->token = 'Bearer ' . $token;

        //  异步记录用户登陆时间
        dispatch(new RecordLastLoginAt($request->user()));

        return $this->res(Code::Success, $request->user(), '登陆成功');
    }

    public function destroy()
    {
        Auth::logout();

        return $this->res(Code::Success, [], '退出登录成功');
    }
}
