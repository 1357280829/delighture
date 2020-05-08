<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ESExampleController extends Controller
{
    protected $es;

    public function __construct()
    {
        $this->es = app('es');
    }

    public function demo1()
    {
        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    //  搜索 description 字段中含有 'returning' 或者 'recovered' 的文档
                    'match' => ['description' => 'returning recovered'],
                    //  搜索 description 字段中含有 'returning recovered' 的文档
//                    'match_phrase' => ['description' => 'returning recovered'],
                ],
            ],
        ];

        $result = $this->es->search($params);

        print_r($result);
    }

    public function demo2()
    {
        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    //  查询所有的文档（注意 ES-PHP 不会把空数组转换成空对象到 ES 中，要手动控制类型）
                    'match_all' => (object) [],
                ],
                'sort' => [
                    'id' => 'desc',
                ],
                'from' => 10,
                'size' => 10,
            ],
        ];

        $result = $this->es->search($params);

        print_r($result);
    }

    public function demo3()
    {
        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => ['description' => 'returning'],
                        ],
                        'must_not' => [
                            //  注意 keyword 类型的字段只支持完整匹配
                            'match' => ['nickname' => '蔺志强'],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->es->search($params);

        print_r($result);
    }

    public function demo4()
    {
        $params = [
            'index' => 'users',
        ];

        $result = $this->es->indices()->getMapping($params);

        print_r($result);
    }

    public function demo5()
    {
        $params = [
            'index' => 'users',
        ];

        $result = $this->es->search($params);

        print_r($result);
    }

    public function demo6()
    {
        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => ['match_all' => (object) []],
                        'filter' => [
                            'range' => [
                                'id' => [
                                    'gte' => 101,
                                    'lte' => 200
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->es->search($params);

        print_r($result);
    }
}
