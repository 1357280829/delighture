[![冰菓](https://cdn.learnku.com/uploads/images/202003/18/33459/0w8NdTvSwl.jpeg!large "冰菓")](https://cdn.learnku.com/uploads/images/202003/18/33459/0w8NdTvSwl.jpeg!large "冰菓")

# 前言

&emsp;&emsp;&emsp;&emsp;这篇文章旨在记录 API 脚手架的搭建过程，搭建这个脚手架是为了整合自己在社区学到的内容 ，同时也是为了以后在新项目面前能迅速拿出实用的干货。

&emsp;&emsp;&emsp;&emsp;脚手架里边有很多地方都是可以再次改造成自己业务所需要的样子，比如自定义的状态码、响应格式、用户表结构以及登陆逻辑等等。

&emsp;&emsp;&emsp;&emsp;我从18年毕业工作到现在，一直在关注着 LearnKu 社区的动态，也在社区上学到很多内容，同时感谢社区，让自己找到前进的方向。

Delighture Github地址：https://github.com/1357280829/delighture

# 搭建过程

## 1 安装 Laravel

&emsp;&emsp;&emsp;&emsp;安装命令： `composer create-project --prefer-dist laravel/laravel delighture`

&emsp;&emsp;&emsp;&emsp;该脚手架基于 `Laravel 7.x` 版本。

## 2 基础配置

1. 准备好数据库相关配置
2. 在 `config/app.php` 文件中更新配置 `'timezone' => 'Asia/Shanghai'`
3. 新建中间件 `AcceptHeader` ,为请求自动添加请求头 `Accept:application/json` ，并在 `Kernel` 文件中将其加入，中间件和  `Kernel` 的文件内容如下：

<br/>

*app/Http/Middleware/AcceptHeader.php*
```
<?php

namespace App\Http\Middleware;

use Closure;

class AcceptHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}

```

<br/>

*app/Http/Kernel.php*
```
		·
		·
		·
    protected $middlewareGroups = [
		·
		·
		·
        'api' => [
            //  统一json响应
            \App\Http\Middleware\AcceptHeader::class,

            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
		·
		·
		·
```

## 3 自定义状态码 和 构建全局统一响应

&emsp;&emsp;&emsp;&emsp;这里我没有使用 `dingo/api` 等有关响应的扩展包，这是为了统一全局的响应，即使是异常的响应，我也想统一其响应格式；同时我希望只用自定义状态码来控制逻辑而非http状态码。这些工作都是为了能够方便和前端对接而准备的。

### 3.1 自定义状态码

1. 首先我们使用 `Laravel-Enum` 扩展包来构建我们的自定义状态码枚举类，安装命令： `composer require bensampo/laravel-enum`
2. 创建自定义状态码文件：`php artisan make:enum Code`
3. 自定义状态码文件内容如下，里边已经定义好了一些后面会用到的状态码，直接复制就好，后面可以再去研究状态码的意义

<br/>

*app/Enums/Code.php*
```
<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * 全局状态码
 *
 * Class Code
 * @package App\Enums
 */
final class Code extends Enum
{
    //  成功
    const Success = 1;

    //  参数验证通用错误
    const FailedValidate = 10000;

    //  用户密码不正确
    const FailedLogin = 20001;
    //  Token不存在
    const MissedToken = 20002;
    //  用户未登陆
    const MissedAuthorization = 20003;
    //  Token已完全过期
    const OverdueToken = 20004;
    //  Token加入黑名单
    const TokenBlacklisted = 20005;
    //  无效的Token
    const InvalidToken = 20006;

    public static function getDescription($value): string
    {
        $descriptions = [
            self::Success => '请求成功',

            self::FailedValidate => '参数验证错误',

            self::FailedLogin => '用户密码不正确',
            self::MissedToken => 'Token不存在',
            self::MissedAuthorization => '用户未登陆',
            self::OverdueToken => 'Token已完全过期',
            self::TokenBlacklisted => 'Token已被加入黑名单',
            self::InvalidToken => '无效的Token',
        ];

        return $descriptions[$value] ?? '未知的状态码';
    }
}
```

### 3.1 构建全局统一响应

1. 在Laravel自带的控制器基类里加入方法 `res()` 作为我们的控制器响应，文件内容如下：

<br/>

*app/Http/Controllers/Controller.php*
```
<?php

namespace App\Http\Controllers;

use App\Enums\Code;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function res($code = Code::Success, $data = [], $message = '')
    {
        return response()->json([
            'message' => $message ?: (Code::getDescription($code) ?? '未知错误'),
            'custom_code' => $code,
            'data' => $data,
        ]);
    }
}
```

2. 新建一个自定义的异常响应，命令 `php artisan make:exception CustomException` ，其文件内容如下：

<br/>

*app/Exceptions/CustomException.php*
```
<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    /**
     * 将异常渲染至 HTTP 响应值中
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage() ?: '客户端异常',
            'custom_code' => $this->getCode(),
        ], 400);
    }
}
```

3. （后面补充）

## 4 构建用户模型 和 实现基于JWT的授权认证

### 4.1 构建用户模型

1. 设计用户表，并创建用户迁移文件，最重要的是 `account` 和 `password` 两个字段，另外注意加入软删除，创建命令 `php artisan make:migration create_users_table` ，文件内容如下：

<br/>

*database/migrations/xxxxx_create_users_table.php*
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('account')->unique()->comment('账号');
            $table->string('password')->comment('密码');
            $table->string('nickname')->comment('昵称');
            $table->string('phone')->nullable()->comment('手机号');
            $table->string('email')->nullable()->comment('邮箱');
            $table->timestamps();

            //  软删除
            $table->softDeletes();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

2. 创建用户模型，命令 `php artisan make:model Models/User`

<br/>

*app/Models/User.php*
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $hidden = ['password', 'deleted_at'];

    protected $fillable = [
        'account', 'password', 'nickname', 'phone', 'email'
    ];
}
```

3. 在 `config/auth.php` 文件中修改授权用户配置：

<br/>

*config/auth.php*
```
	·
	·
	·
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],
	·
	·
	·
```

### 4.2 实现基于JWT的授权认证

1. 安装扩展包 `composer require tymon/jwt-auth`
2. 发布配置文件 `config/jwt.php`，命令 `php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"`
3. 修改授权用户模型，文件内容修改如下：

<br/>

*app/Models/User.php*
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    protected $hidden = ['password', 'deleted_at'];

    protected $fillable = [
        'account', 'password', 'nickname', 'phone', 'email'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

4. 修改授权配置文件 `config/auth.php` 守卫（Guard）的配置：

<br/>

*config/auth.php*
```
	·
	·
	·
    'defaults' => [
		//  这里把默认守卫改为 api
        'guard' => 'api',
        'passwords' => 'users',
    ],
	·
	·
	·
	'guards' => [
		'web' => [
		'driver' => 'session',
		'provider' => 'users',
		],

	'api' => [
		//  这里把驱动改为 jwt
		'driver' => 'jwt',
		'provider' => 'users',
		'hash' => false,
		],
	],
	·
	·
	·
```

5. 实现无痛刷新JWT授权认证的功能，新建中间件 `RefreshToken` ，命令 `php artisan make:middleware RefreshToken` ，其文件内容如下：

<br/>

*app/Http/Middleware/RefreshToken.php*
```
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
```

6. 接着正式使用JWT于用户登陆注销功能中，创建控制器 `Api/AuthorizationsController` ，命令 `php artisan make:controller Api/AuthorizationsController`；创建表单验证 `Api/AuthorizationRequest` 及其基类 `Request` ，命令分别为 `php artisan make:request Api/AuthorizationRequest` 和 `php artisan make:request Request` ，三个文件的内容如下：

<br/>

*app/Http/Controllers/Api/AuthorizationsController.php*
```
<?php

namespace App\Http\Controllers\Api;

use App\Enums\Code;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationRequest;
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

        return $this->res(Code::Success, $request->user(), '登陆成功');
    }

    public function destroy()
    {
        Auth::logout();

        return $this->res(Code::Success, [], '退出登录成功');
    }
}
```

<br/>

*app/Http/Requests/Api/AuthorizationRequest.php*
```
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
```

<br/>

*app/Http/Requests/Request.php*
```
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }
}
```

7. 在 `routes/api.php` 创建相关路由

<br/>

*routes/api.php*
```
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->group(function () {

    Route::post('authorizations', 'AuthorizationsController@store');
    Route::delete('authorizations', 'AuthorizationsController@destroy');

    Route::middleware('token.refresh')->group(function () {
        //  这里加入需要授权认证的接口
    });
});
```

8. 适当的提取 `config/jwt.php` 文件中的相关配置到 `.env` 文件中，如：

<br/>

*.env*
```
·
·
·
JWT_TTL=60
JWT_REFRESH_TTL=20160
JWT_BLACKLIST_GRACE_PERIOD=2
·
·
·
```

## 总结

&emsp;&emsp;&emsp;&emsp;至此我们的脚手架已经大致搭建完成，这里主要是描述造轮子的过程，具体的意义可以查看后面给出的相关链接，或者自行在社区摸索，后面我也会不断地更新完善该脚手架，让其在实战项目中发挥更大的作用。

## 相关链接

1. [L02 Laravel 教程 - Web 开发实战进阶 ( Laravel 6.x )](https://learnku.com/courses/laravel-intermediate-training/6.x)
2. [L03 Laravel 教程 - 实战构架 API 服务器 ( Laravel 6.x )](https://learnku.com/courses/laravel-advance-training/6.x)
3. [Laravel 中的异常处理](https://learnku.com/laravel/t/8783/exception-handling-in-laravel "Laravel 中的异常处理")
4. [使用 Jwt-Auth 实现 API 用户认证以及无痛刷新访问令牌](https://learnku.com/articles/7264/using-jwt-auth-to-implement-api-user-authentication-and-painless-refresh-access-token "使用 Jwt-Auth 实现 API 用户认证以及无痛刷新访问令牌")
5. [JWT 完整使用详解](https://learnku.com/articles/10885/full-use-of-jwt "JWT 完整使用详解")
6. [在 Laravel 中使用枚举](https://learnku.com/laravel/t/36091)
7. [手摸手教你让 Laravel 开发 API 更得心应手](https://learnku.com/articles/25947)