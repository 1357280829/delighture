<?php

namespace App\Console\Commands\Elasticsearch;

use App\Models\User;
use Illuminate\Console\Command;

class SyncUsers extends Command
{
    protected $signature = 'es:sync-users';

    protected $description = '将 users 数据同步到 Elasticsearch';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //  获取 Elasticsearch 对象
        $es = app('es');

        User::query()
            ->chunkById(100, function ($users) use ($es) {
                $this->info(sprintf('正在同步 users 表中 ID 范围为 %s 至 %s 的数据', $users->first()->id, $users->last()->id));

                //  初始化请求体
                $req = ['body' => []];
                //  遍历商品
                foreach ($users as $user) {
                    //  将商品模型转为 Elasticsearch 所用的数组
                    $data = $user->toESArray();

                    $req['body'][] = [
                        'index' => [
                            '_index' => 'users',
                            '_id'    => $data['id'],
                        ],
                    ];
                    $req['body'][] = $data;
                }

                try {
                    //  使用 bulk 方法批量创建
                    $es->bulk($req);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            });

        $this->info('同步 Elasticsearch 成功');
    }
}
