<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class AuthorizationRequest extends Request
{
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'account' => 'required|between:6,12|exists:users,account',
                    'password' => 'required|confirmed',
                ];
        }
    }
}
