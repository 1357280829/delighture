<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();
        $password = Hash::make(123456);

        DB::table('users')->insert([
            'account' => 'zhonghang',
            'password' => $password,
            'nickname' => '钟航',
            'phone' => 18813299655,
            'email' => '1357280829@qq.com',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $users = factory(User::class)
            ->times(5000)
            ->make()
            ->toArray();

        foreach ($users as &$user) {
            $user['password'] = $password;
            $user['created_at'] = $now;
            $user['updated_at'] = $now;
        }

        DB::table('users')->insert($users);
    }
}
