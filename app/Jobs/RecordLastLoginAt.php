<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecordLastLoginAt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        //  -------------------------------------------------------
        //  注意：队列任务构造器中接收了 Eloquent 模型，将会只序列
        //  化模型的 ID，在执行的时候才会去检索模型
        //  -------------------------------------------------------
        $this->user = $user;
    }

    public function handle()
    {
        //  -------------------------------------------------------
        //  注意：执行队列任务最好避免 Eloquent 模型接口调用，因为
        //  若是在同是使用了模型监控器的 Observer 中分发任务，会陷
        //  入调用死循环
        //  -------------------------------------------------------
        DB::table('users')->where('id', $this->user->id)->update(['last_login_at' => now()]);
    }
}
