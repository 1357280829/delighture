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
