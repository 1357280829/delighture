<?php

namespace App\Http\Middleware;

use App\Enums\Code;
use App\Exceptions\CustomException;
use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware as JWTBaseMiddleware;

class RefreshToken extends JWTBaseMiddleware
{
    public function handle($request, Closure $next)
    {
        //  Step1：检测token字段是否存在
        if (! $this->auth->parser()->setRequest($request)->hasToken()) {
            throw new CustomException(Code::getDescription(Code::MissedToken), Code::MissedToken);
        }

        try {

            //  Step2：检测用户是否登录
            $user = $this->auth->parseToken()->authenticate();
            if ($user) {
                //  token认证通过
                return $next($request);
            }

            //  用户已登陆，但是用户数据不存在
            throw new CustomException(Code::getDescription(Code::MissedAuthorization), Code::MissedAuthorization);

        } catch (TokenExpiredException $exception) {

            try {
                //  Step4：刷新用户的 token
                $token = $this->auth->refresh();
                //  Step5：使用一次性登录以保证此次请求的成功
                Auth::onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
            } catch (TokenExpiredException $exception) {
                //  token超过刷新时间
                throw new CustomException(Code::getDescription(Code::OverdueToken), Code::OverdueToken);
            } catch (TokenBlacklistedException $exception) {
                //  并发调用带过期token的接口会走到这里，设置 JWT_BLACKLIST_GRACE_PERIOD 参数解决
                throw new CustomException(Code::getDescription(Code::TokenBlacklisted), Code::TokenBlacklisted);
            }

        } catch (TokenBlacklistedException $exception) {
            //  token被加入黑名单
            throw new CustomException(Code::getDescription(Code::TokenBlacklisted), Code::TokenBlacklisted);
        } catch (TokenInvalidException $exception) {
            //  无效的token
            throw new CustomException(Code::getDescription(Code::InvalidToken), Code::InvalidToken);
        }

        //  Step6：在响应头中返回新的token
        return $this->setAuthenticationHeader($next($request), $token);
    }
}
